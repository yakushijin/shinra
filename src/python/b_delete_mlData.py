
import logging
import MySQLdb
import pathlib
import os
import pprint
from datetime import datetime,timedelta
import masterDbConnect
import logCommon
import pandas as pd
import numpy  as np
from cassandra.cluster import Cluster


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

dbUserGetSql = "select userId as id from M_User"
dbGroupGetSql = "select groupId as id from M_Group"
dbTabGetSql = "select tabId as id from T_Tab"
 
mlUserGetSql = "select userId as id from userWordset group by userId"
mlGroupGetSql = "select groupId as id from groupWordSet group by groupId"
mlTabGetSql = "select tabId as id from tabWordSet group by tabId"

mlUserDeleteSql = "delete from userWordset where userId = "
mlGroupGetDeleteSql = "delete from groupWordSet where groupId = "
mlTabGetDeleteSql = "delete from tabWordSet where tabId = "

#ログ関連
summaryLog = logCommon.logTextCreate('s_delete_mlData')
detailLog = logCommon.logTextCreate('d_delete_mlData_'+str(nowDate))

successCount = 0
resultCode = 0

#==================================================================
#データ抽出、削除共通処理
#==================================================================
def mlDataDelete(dbGetSql,mlGetSql,mlDeleteSql):
    #DBから各テーブルのIDをすべて取得
    subCursor.execute(dbGetSql)
    dbRows = subCursor.fetchall()

    #NOSQLDBから各テーブルのIDをすべて取得
    mlRows = session.execute(mlGetSql)

    #配列変換
    dbRowsArray = []
    mlRowsArray = []
    for row in dbRows:
        dbRowsArray.append(row[0])
    for row in mlRows:
        mlRowsArray.append(row.id)

    #NOSQLDBの値はデータフレームに変換
    mlRowsArrayDf = {'id': mlRowsArray}
    mlIdData = pd.DataFrame.from_dict(mlRowsArrayDf)    

    #DBのIDとNOSQLのIDの差分を抽出する。DBに無くて、NOSQLDBにあるIDを取り出す。
    notIdList = mlIdData[~mlIdData['id'].isin(dbRowsArray)]['id']
    #取り出したIDをNOSQLDBから削除する
    for data in notIdList.values:
        print(dbGetSql + str(data))
        sql = mlDeleteSql + str(data)
        session.execute(sql)

#==================================================================
#実行処理
#==================================================================
#実行開始時間記録
startTime = logCommon.startEndDateFormat()

#masterDBから各ワークスペースの情報を取得
cursor.execute(targetGetSql)
rows = cursor.fetchall()
targetDbCount = len(rows)

#ワークスペースごとのDBに接続し、各処理を実施
for row in rows:
    #DB接続定義
    companyId = row[0]
    companyName = row[1]
    dbUser = row[2]
    dbPassword = row[3]
    dbHost = row[4]
    mlHost = row[5]
    categorySaveDay = row[6]

    subConnection = MySQLdb.connect(
    host=dbHost,
    user=dbUser,
    passwd=dbPassword,
    db=dbUser)
    subCursor = subConnection.cursor()

    #NOSQL接続定義
    cluster = Cluster([mlHost])
    session = cluster.connect(dbUser.lower())

    #実行ごとのログ情報定義
    exeNow = datetime.now()
    exeResultCode = 0
    exeResulttext = ""
    
    #ユーザ
    try:
        mlDataDelete(dbGetSql=dbUserGetSql,mlGetSql=mlUserGetSql,mlDeleteSql=mlUserDeleteSql) 
    except: 
        resultCode = 1
        exeResultCode += 1
        #実行ごとのログ出力
        detailResult = logCommon.detailLogOutputFormat(date=exeNow,resultCode=exeResultCode,companyId=companyId,companyName=companyName,dbHost=dbHost,exeResulttext=exeResulttext)
        with detailLog.open(mode="a") as f:
            f.write(detailResult)
        pass
    
    #グループ
    try:
        mlDataDelete(dbGetSql=dbGroupGetSql,mlGetSql=mlGroupGetSql,mlDeleteSql=mlGroupGetDeleteSql)
    except: 
        resultCode = 1
        exeResultCode += 3
        #実行ごとのログ出力
        detailResult = logCommon.detailLogOutputFormat(date=exeNow,resultCode=exeResultCode,companyId=companyId,companyName=companyName,dbHost=dbHost,exeResulttext=exeResulttext)
        with detailLog.open(mode="a") as f:
            f.write(detailResult)
        pass

    #タブ
    try:
        mlDataDelete(dbGetSql=dbTabGetSql,mlGetSql=mlTabGetSql,mlDeleteSql=mlTabGetDeleteSql)
    except Exception as e: 
        exeResultCode += 5
        exeResulttext = e
        #実行ごとのログ出力
        detailResult = logCommon.detailLogOutputFormat(date=exeNow,resultCode=exeResultCode,companyId=companyId,companyName=companyName,dbHost=dbHost,exeResulttext=exeResulttext)
        with detailLog.open(mode="a") as f:
            f.write(detailResult)
        pass

    if exeResultCode == 0:
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
