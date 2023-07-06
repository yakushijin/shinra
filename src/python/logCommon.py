
import logging
import pathlib
from datetime import datetime,timedelta

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


def logTextCreate(fileName):
    logDir = "/var/log/bat/"
    log = pathlib.Path(logDir + fileName+'.log')
    if log.exists():
        log.touch()
    return log

def startEndDateFormat():
    date = datetime.now().strftime("%H:%M:%S")
    return date


def summaryLogOutputFormat(date,startTime,endTime,targetDbCount,successCount,resultCode):
    logSplitStr = " "
    summaryResult = str(date) + logSplitStr + startTime + logSplitStr + endTime + logSplitStr +str(targetDbCount) + logSplitStr + str(successCount) + logSplitStr + str(resultCode)+ "\n"
    return summaryResult

def detailLogOutputFormat(date,resultCode,companyId,companyName,dbHost,exeResulttext):
    logSplitStr = " "
    detailResult = str(date) + logSplitStr + str(resultCode) + logSplitStr + str(companyId)+ logSplitStr + "\"" + companyName + "\"" + logSplitStr +str(dbHost)+ logSplitStr + "\n" + str(exeResulttext)
    return detailResult
