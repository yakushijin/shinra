
import logging
import MySQLdb
import pathlib
import os
import pprint
from datetime import datetime,timedelta
import masterDbConnect
import logCommon


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
targetDeleteSql = "DELETE FROM T_Performance WHERE performanceDay = '" + yesterdayDate + "'"
targetInsertSql = " INSERT INTO T_Performance( "\
    "     tabId, "\
    "     performanceDay, "\
    "     notDoneCount, "\
    "     doneCount, "\
    "     percentage, "\
    "     createDay, "\
    "     updateDay "\
    " ) "\
    " SELECT "\
    "     a.tabId, "\
    "     '"+yesterdayDate+"' as performanceDay, "\
    "     IFNULL(sum(c.notDoneCount), 0), "\
    "     IFNULL(sum(c.doneCount), 0), "\
    "     case IFNULL(sum(c.notDoneCount), 0) + IFNULL(sum(c.doneCount), 0) "\
    "         when 0 then 0 "\
    "         ELSE ROUND(IFNULL(sum(c.doneCount), 0) / (IFNULL(sum(c.notDoneCount), 0) + IFNULL(sum(c.doneCount), 0)) * 100) "\
    "     END AS percentage, "\
    "     now() as createDay, "\
    "     now() as updateDay "\
    " FROM "\
    "     T_Tab a "\
    "     LEFT JOIN "\
    "         ( "\
    "         SELECT "\
    "             a.tabId, "\
    "             ct.userId, "\
    "             ct.notDoneCount, "\
    "             ct.doneCount "\
    "         FROM "\
    "             T_Tab a "\
    "             LEFT JOIN "\
    "                 ( "\
    "                 SELECT "\
    "                     COUNT(*) AS notDoneCount, "\
    "                     0 AS doneCount, "\
    "                     tabId, "\
    "                     userId "\
    "                 FROM "\
    "                     T_Category "\
    "                 WHERE "\
    "                     categoryDone = 0 "\
    "                 GROUP BY "\
    "                     tabId, "\
    "                     userId "\
    "                 UNION all "\
    "                 SELECT "\
    "                     0 AS notDoneCount, "\
    "                     COUNT(*) AS doneCount, "\
    "                     tabId, "\
    "                     userId "\
    "                 FROM "\
    "                     T_Category "\
    "                 WHERE "\
    "                     categoryDone = 1 "\
    "                 GROUP BY "\
    "                     tabId, "\
    "                     userId "\
    "                 ) ct "\
    "             ON  ( a.tabId = ct.tabId ) "\
    "         ) c "\
    "     ON  ( a.tabId = c.tabId ) "\
    " WHERE "\
    "     groupFlg = 1 "\
    " GROUP BY "\
    "     a.tabId"

#ログ関連
summaryLog = logCommon.logTextCreate('s_insert_performance')
detailLog = logCommon.logTextCreate('d_insert_performance_'+str(nowDate))

successCount = 0
resultCode = 0


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

    subConnection = MySQLdb.connect(
    host=dbHost,
    user=dbUser,
    passwd=dbPassword,
    db=dbUser)
    subConnection.autocommit(False)
    cursor = subConnection.cursor()

    #実行ごとのログ情報定義
    exeNow = datetime.now()
    exeResultCode = 0
    exeResulttext = ""

    #実績を集計し、集計結果を指定テーブルへ格納
    try:
        cursor.execute(targetDeleteSql)
        cursor.execute(targetInsertSql)
        subConnection.commit()
        successCount += 1
        exeResultCode = 0
    except Exception as e: 
        subConnection.rollback()
        resultCode = 1
        exeResultCode = 1
        exeResulttext = e
        pass

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
