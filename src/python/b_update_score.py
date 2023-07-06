import pandas as pd
import numpy  as np
import spacy
from cassandra.cluster import Cluster
import logging
import socket
import MySQLdb
from json import loads
import masterDbConnect
import logCommon
from datetime import datetime,timedelta
import string

# ログレベルを DEBUG に変更
# logging.basicConfig(filename='/var/www/logger.log', level=logging.DEBUG)

# import sys
# args = sys.argv
# dbUser = args[1]
# dbPassword = args[2]
# dbHost = args[3]
# mlUser = args[4]

#==================================================================
#基本定義情報
#==================================================================
#日付
now = datetime.now()
nowDate = now.strftime("%Y-%m-%d")

#DB接続定義
connection =masterDbConnect.getMasterDbConnection()
cursor = connection.cursor()

#SQL
targetGetSql = masterDbConnect.getMasterDbCompanyInfo()
categoryGetSql = "SELECT a.userId,a.groupId,a.tabId,a.categoryName,a.categoryId "\
    " FROM T_Category a "\
    " INNER JOIN T_Tab b ON (a.tabId = b.tabId) "\
    " where learnedFlg = 0 AND categoryDone = 1 AND b.groupFlg = 1 "

taskGetSql = "SELECT a.userId,a.groupId,a.tabId,a.taskName,a.taskId "\
    " FROM T_Task a "\
    " INNER JOIN T_Tab b ON (a.tabId = b.tabId) "\
    " where learnedFlg = 0 AND taskDone = 1 AND b.groupFlg = 1 "

#ログ関連
summaryLog = logCommon.logTextCreate('s_update_score')
detailLog = logCommon.logTextCreate('d_update_score_' +str(nowDate))

successCount = 0
resultCode = 0

#==================================================================
#機械学習処理要メソッド定義
#==================================================================
def noSqlUpdate(dbRows,point):
    nlp = spacy.load('ja_ginza')
    errorIdList = []
    mlResultCode = 0

    for row in dbRows:

        userId = row[0]
        groupId = row[1]
        tabId = row[2]
        text = row[3]
        id = row[4]

        #半角記号を削除
        getText = text.translate(str.maketrans( '', '',string.punctuation))

        doc = nlp(getText)
        
        #自然言語の分割及び種別判定、必要なものを連想配列に格納
        try:            
            analysisWordList = []
            for sent in doc.sents:
                for token in sent:
                    if token.pos_ == 'PROPN' or token.pos_ == 'NOUN' or token.pos_ == 'NUM' or token.pos_ == 'VERB':
                        wordSet = {
                            "word":token.orth_,
                            "wordType":token.pos_,
                            "wordDetail":token.tag_,
                            "dependType":token.dep_,
                            "depend":"",
                            "dependedOn":""
                        }
                        analysisWordList.append(wordSet)
        except: 
            errorIdList.append(id)
            continue


        #解析及びフィルタ済みの言語リストにて、親子関係で依存する言語を結合する
        try:            
            dependJoinWordListTmp = []    
            joinWord = ""
            #ワードの結合処理1。ワードを結合させる      
            for i in analysisWordList:
                #指定したタイプのワードの場合最初に前の結合対象ワードで結合する（1ループ目は必ず空、2ループ目以降に結合ワードが出る可能性がある）
                # if i["wordType"] == 'PROPN' or i["wordType"] == 'NOUN' or i["wordType"] == 'NUM':
                if  i["wordType"] == 'NUM':
                    i["word"] = joinWord + i["word"]
                #結合ワードを初期化
                joinWord = "" 
                #現在のワードが依存関係があるワードの場合、結合ワードに現在のワードを入れ、次回以降のループ時に次のワードに結合させる
                if i["dependType"] == 'compound':
                    joinWord = i["word"]
                #現在のワード情報を新しいリストに格納する
                dependJoinWordListTmp.append(i)

            #ワードの結合処理2。結合されたワードをリストから削除する（結合されたワードはリスト内で重複する為）
            #（例）AとBを結合して生成されるリスト→1.A 2.AB　1のレコードはいらないので削除   
            dependJoinWordList = []   
            for i in dependJoinWordListTmp:
                dependJoinWordList.append(i)
                # if i["dependType"] != 'compound':
                #     dependJoinWordList.append(i)
        except: 
            errorIdList.append(id)
            continue


        #結合済みのリストにて、言語ごとの依存関係を解析し、リストに格納する
        try:            
            dependAddWordList = []  
            dependWord = ""
            for i in dependJoinWordList:
                i["depend"] = dependWord
                if i["dependType"] == 'compound':
                    for j in dependJoinWordList:
                        i["dependedOn"] = j["word"]
                        dependWord = i["word"]
                if i["dependType"] == 'nmod':
                    for j in dependJoinWordList:
                        if j["dependType"] == 'obj' or j["dependType"] == 'obl' or j["dependType"] == 'ROOT':
                            i["dependedOn"] = j["word"]
                            dependWord = i["word"]
                if i["dependType"] == 'obj':
                    for j in dependJoinWordList:
                        if j["dependType"] == 'ROOT':
                            i["dependedOn"] = j["word"] 
                            dependWord = i["word"]
                if i["dependType"] == 'obl':
                    for j in dependJoinWordList:
                        if j["dependType"] == 'VERB':
                            i["dependedOn"] = j["word"] 
                            dependWord = i["word"]    
                if i["dependType"] == 'iobj':
                    for j in dependJoinWordList:
                        if j["wordType"] == 'VERB':
                            i["dependedOn"] = j["word"] 
                            dependWord = i["word"]              
                dependAddWordList.append(i)
        except: 
            errorIdList.append(id)
            continue

        try:            
            #登録処理
            for i in dependAddWordList:
                #ユーザ単位での格納
                selectSql = "select score from userWordSet where userId = "+str(userId)+" and word = '" + i["word"] + "' and dependWord = '" + i["depend"] + "' and dependedOnWord = '" + i["dependedOn"] + "' " 

                rows = session.execute(selectSql)

                if rows == []:
                    updateScore = point
                else:
                    updateScore = rows[0].score + point

                updateSql = "INSERT INTO userWordSet (userId, word, dependWord,dependedOnWord,score, createDate) VALUES ( "+str(userId)+",'"+i["word"]+"','"+i["depend"]+"','"+i["dependedOn"]+"', "+str(updateScore)+", toDate(now()))"
                session.execute(updateSql)

                # グループ単位での格納
                selectSql = "select score from groupWordSet where groupId = "+str(groupId)+" and word = '" + i["word"] + "' and dependWord = '" + i["depend"] + "' and dependedOnWord = '" + i["dependedOn"] + "' " 

                rows = session.execute(selectSql)

                if rows == []:
                    updateScore = point
                else:
                    updateScore = rows[0].score + point

                updateSql = "INSERT INTO groupWordSet (groupId, word, dependWord,dependedOnWord,score, createDate) VALUES ( "+str(groupId)+",'"+i["word"]+"','"+i["depend"]+"','"+i["dependedOn"]+"', "+str(updateScore)+", toDate(now()))"
                session.execute(updateSql)

                # タブ単位での格納
                selectSql = "select score from tabWordSet where tabId = "+str(tabId)+" and word = '" + i["word"] + "' and dependWord = '" + i["depend"] + "' and dependedOnWord = '" + i["dependedOn"] + "' " 

                rows = session.execute(selectSql)

                if rows == []:
                    updateScore = point
                else:
                    updateScore = rows[0].score + point

                updateSql = "INSERT INTO tabWordSet (tabId, word, dependWord,dependedOnWord,score, createDate) VALUES ( "+str(tabId)+",'"+i["word"]+"','"+i["depend"]+"','"+i["dependedOn"]+"', "+str(updateScore)+", toDate(now()))"
                session.execute(updateSql)
        except: 
            errorIdList.append(id)
            continue

    return errorIdList

#全てのレコードの件数の合計を計算する 
def noSqlDbUesData():
    userWordSetSql = "SELECT count(*) as count FROM userWordSet"
    groupWordSetSql = "SELECT count(*) as count FROM groupWordSet"
    tabWordSetSql = "SELECT count(*) as count FROM tabWordSet"

    userWordSetCount = session.execute(userWordSetSql)
    groupWordSetCount = session.execute(groupWordSetSql)
    tabWordSetCount = session.execute(tabWordSetSql)

    noSqlCount = userWordSetCount[0].count + groupWordSetCount[0].count + tabWordSetCount[0].count

    return noSqlCount



#==================================================================
#実行処理
#==================================================================
#実行開始時間記録
startTime = logCommon.startEndDateFormat()

#masterDBから各ワークスペースの情報を取得
cursor.execute(targetGetSql)
rows = cursor.fetchall()
targetDbCount = len(rows)

for row in rows:
    #DB接続定義
    companyId = row[0]
    companyName = row[1]
    dbUser = row[2]
    dbPassword = row[3]
    dbHost = row[4]
    mlHost = row[5]

    subConnection = MySQLdb.connect(
    host=dbHost,
    user=dbUser,
    passwd=dbPassword,
    db=dbUser)
    subConnection.autocommit(False)
    subCursor = subConnection.cursor()

    #NOSQL接続定義
    cluster = Cluster([mlHost])
    session = cluster.connect(dbUser.lower())

    #実行ごとのログ情報定義
    exeNow = datetime.now()
    exeResultCode = 0
    exeResulttext = ""

    #カテゴリとタスクのデータを集計する
    try:
        subCursor.execute(categoryGetSql)
        categoryDbRows = subCursor.fetchall()
        subCursor.execute(taskGetSql)
        taskDbRows = subCursor.fetchall()
        exeResultCode = 0
    except Exception as e: 
        resultCode = 1
        exeResultCode = 1
        exeResulttext = e

        #実行ごとのログ出力
        detailResult = logCommon.detailLogOutputFormat(date=exeNow,resultCode=exeResultCode,companyId=companyId,companyName=companyName,dbHost=dbHost,exeResulttext=exeResulttext)
        with detailLog.open(mode="a") as f:
            f.write(detailResult)
        continue


    #集計したデータを基に機械学習処理を実行する
    try:
        resultCategoryIdList = noSqlUpdate(dbRows = categoryDbRows,point = 2)
        resultTaskIdList = noSqlUpdate(dbRows = taskDbRows,point = 1)
        errorCategoryId = set(resultCategoryIdList)
        errorTaskId = set(resultTaskIdList)
    except Exception as e: 
        resultCode = 1
        exeResultCode = 2
        exeResulttext = e

        #実行ごとのログ出力
        detailResult = logCommon.detailLogOutputFormat(date=exeNow,resultCode=exeResultCode,companyId=companyId,companyName=companyName,dbHost=dbHost,exeResulttext=exeResulttext)
        with detailLog.open(mode="a") as f:
            f.write(detailResult)
        continue

    try:
        for row in categoryDbRows:
            subCursor.execute("update T_Category set learnedFlg = 1 where categoryId = " + str(row[4]))

        for row in taskDbRows:
            subCursor.execute("update T_Task set learnedFlg = 1 where taskId = " + str(row[4]))

        if errorCategoryId:
            exeResultCode = 4
            for row in errorCategoryId:
                subCursor.execute("update T_Category set learnedFlg = 9 where categoryId = " + str(row))

        if errorTaskId:
            exeResultCode = 5
            for row in errorTaskId:
                subCursor.execute("update T_Task set learnedFlg = 9 where taskId = " + str(row))

        subConnection.commit()
    except Exception as e: 
        subConnection.rollback()
        resultCode = 1
        exeResultCode = 3
        exeResulttext = e

        #実行ごとのログ出力
        detailResult = logCommon.detailLogOutputFormat(date=exeNow,resultCode=exeResultCode,companyId=companyId,companyName=companyName,dbHost=dbHost,exeResulttext=exeResulttext)
        with detailLog.open(mode="a") as f:
            f.write(detailResult)
        continue

    try:
        noSqlUesCount =  noSqlDbUesData()
        noSqlDataRecodeSize = 100
        noSqlDataAllRecodeSize = noSqlUesCount * noSqlDataRecodeSize
        noSqlUesData = round(noSqlDataAllRecodeSize/1024/1024/1024,4)
        if noSqlUesData < 0.0001:
            noSqlUesData = 0.0001
    except Exception as e: 
        resultCode = 1
        exeResultCode = 6
        exeResulttext = e
        
        #実行ごとのログ出力
        detailResult = logCommon.detailLogOutputFormat(date=exeNow,resultCode=exeResultCode,companyId=companyId,companyName=companyName,dbHost=dbHost,exeResulttext=exeResulttext)
        with detailLog.open(mode="a") as f:
            f.write(detailResult)
        continue
   
    try:
        cursor.execute("update G_Company set mlDataUse = " +str(noSqlUesData)+ ", mlDataAllCount = " +str(noSqlUesCount)+ " where companyId = " + str(companyId) )
        connection.commit()   
    except Exception as e: 
        #ここで失敗してもロールバックはしない（影響度極小の為）
        resultCode = 1
        exeResultCode = 7
        exeResulttext = e
        pass

    successCount += 1

    #DB接続クローズ
    subConnection.close()

    #実行ごとのログ出力
    detailResult = logCommon.detailLogOutputFormat(date=exeNow,resultCode=exeResultCode,companyId=companyId,companyName=companyName,dbHost=dbHost,exeResulttext=exeResulttext)
    with detailLog.open(mode="a") as f:
        f.write(detailResult)


#DB接続クローズ
connection.close()

#実行終了時間記録
endTime = logCommon.startEndDateFormat()

#サマリログ出力
summaryResult = logCommon.summaryLogOutputFormat(date=nowDate,startTime=startTime,endTime=endTime,targetDbCount=targetDbCount,successCount=successCount,resultCode=resultCode)
with summaryLog.open(mode="a") as f:
    f.write(summaryResult)

