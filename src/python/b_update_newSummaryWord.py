import pandas as pd
import numpy  as np
import spacy
from cassandra.cluster import Cluster
import logging
import socket
import MySQLdb
from json import loads
from datetime import datetime,timedelta
import pathlib
import os
import pprint
import masterDbConnect
import logCommon
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
yesterday = now - timedelta(days=1)
nowDate = now.strftime("%Y-%m-%d")
yesterdayDate = yesterday.strftime("%Y-%m-%d")

#DB接続定義
connection =masterDbConnect.getMasterDbConnection()
cursor = connection.cursor()

#SQL
targetGetSql = masterDbConnect.getMasterDbCompanyInfo()
categoryGetSql = "SELECT a.userId,a.groupId,a.tabId,a.categoryName,a.categoryId "\
    " FROM T_Category a "\
    " INNER JOIN T_Tab b ON (a.tabId = b.tabId) "\
    " where b.groupFlg = 1 "\
    " AND a.createDay BETWEEN cast('"+yesterdayDate+" 00:00:00' as DATETIME) AND cast('"+nowDate+" 23:59:59' as DATETIME) "

taskGetSql = "SELECT a.userId,a.groupId,a.tabId,a.taskName,a.taskId "\
    " FROM T_Task a "\
    " INNER JOIN T_Tab b ON (a.tabId = b.tabId) "\
    " where b.groupFlg = 1 "\
    " AND a.createDay BETWEEN cast('"+yesterdayDate+" 00:00:00' as DATETIME) AND cast('"+nowDate+" 23:59:59' as DATETIME) "

#ログ関連
summaryLog = logCommon.logTextCreate('s_update_newSummaryWord')
detailLog = logCommon.logTextCreate('d_update_newSummaryWord_' +str(nowDate))

successCount = 0
resultCode = 0

#==================================================================
#機械学習処理要メソッド定義
#==================================================================
def noSqlUpdate(dbRows,point):
    session.execute("truncate newSummaryWordSet")
    nlp = spacy.load('ja_ginza')
    for row in dbRows:

        userId = row[0]
        groupId = row[1]
        tabId = row[2]
        text = row[3]

        #半角記号を削除
        getText = text.translate(str.maketrans( '', '',string.punctuation))

        doc = nlp(getText)
        
        #自然言語の分割及び種別判定、必要なものを連想配列に格納
        analysisWordList = []
        for sent in doc.sents:
            for token in sent:
                if token.pos_ == 'PROPN' or token.pos_ == 'NOUN':
                    wordSet = {
                        "word":token.orth_,
                        "wordType":token.pos_,
                        "wordDetail":token.tag_,
                        "dependType":token.dep_,
                        "depend":"",
                        "dependedOn":""
                    }
                    analysisWordList.append(wordSet)

        #解析及びフィルタ済みの言語リストにて、親子関係で依存する言語を結合する
        dependJoinWordListTmp = []    
        joinWord = ""      
        for i in analysisWordList:
            i["word"] = joinWord + i["word"]
            joinWord = "" 
            if i["dependType"] == 'compound':
                joinWord = i["word"]
            dependJoinWordListTmp.append(i)
        dependJoinWordList = []   
        for i in dependJoinWordListTmp:
            if i["dependType"] != 'compound':
                dependJoinWordList.append(i)

        #登録処理
        for i in dependJoinWordList:
            #ユーザ単位での格納
            selectSql = "select score from newSummaryWordSet where userId = "+str(userId)+" and  groupId = "+str(groupId)+" and tabId = "+str(tabId)+" and word = '" + i["word"] + "'"

            rows = session.execute(selectSql)

            if rows == []:
                updateScore = point
            else:
                updateScore = rows[0].score + point

            updateSql = "INSERT INTO newSummaryWordSet (word, score, userId,groupId,tabId, createDate) VALUES ( '"+i["word"]+"',"+str(updateScore)+","+str(userId)+","+str(groupId)+","+str(tabId)+", toDate(now()))"
            session.execute(updateSql)

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
    cursor = subConnection.cursor()

    #NOSQL接続定義
    cluster = Cluster([mlHost])
    session = cluster.connect(dbUser.lower())

    #実行ごとのログ情報定義
    exeNow = datetime.now()
    exeResultCode = 0
    exeResulttext = ""
                        
    # nowGetSql = "select word,score,dependword from test.groupWordSet where createdate = '"+nowDate+"'"
    # yesterdayGetSql = "select word,score,dependword from test.groupWordSet where createdate = '"+yesterdayDate+"'"

    # nowRows = session.execute(nowGetSql)
    # yesterdayRows = session.execute(yesterdayGetSql)

    #DBから当日登録されているカテゴリとタスクを取得する
    # cursor.execute("SELECT userId,groupId,tabId,categoryName,categoryId FROM T_Category WHERE createDay BETWEEN '"+nowDate+" 00' AND '"+nowDate+" 12'")
    
    #カテゴリとタスクのデータを集計する
    try:
        cursor.execute(categoryGetSql)
        categoryDbRows = cursor.fetchall()
    # cursor.execute("SELECT userId,groupId,tabId,taskName,taskId FROM T_Task WHERE createDay BETWEEN '"+nowDate+" 00' AND '"+nowDate+" 12'")
        cursor.execute(taskGetSql)
        taskDbRows = cursor.fetchall()

        successCount += 1

    # if len(categoryDbRows)==0 and len(taskDbRows)==0:
    #     conn.sendall("ng".encode(encoding='utf-8'))
    #     break
    except Exception as e: 
        resultCode = 1
        exeResultCode = 1
        exeResulttext = e

        #実行ごとのログ出力
        detailResult = logCommon.detailLogOutputFormat(date=exeNow,resultCode=exeResultCode,companyId=companyId,companyName=companyName,dbHost=dbHost,exeResulttext=exeResulttext)
        with detailLog.open(mode="a") as f:
            f.write(detailResult)
        continue

    #DB接続クローズ
    subConnection.close()

    #集計したデータを基に機械学習処理を実行する
    try:
        noSqlUpdate(dbRows = categoryDbRows,point = 2)
        noSqlUpdate(dbRows = taskDbRows,point = 1)
    except Exception as e: 
        resultCode = 1
        exeResultCode = 2
        exeResulttext = e
        pass
    
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



