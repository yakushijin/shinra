
import MySQLdb
import pathlib

def getMasterDbConnection():
    passDir = "/var/www/python/"
    text = pathlib.Path(passDir +'pass')
    dbPasswordText = text.read_text().replace('\n','')

    dbHost = 'localhost'
    dbUser = 'A_ServiceMaster'
    dbPassword = dbPasswordText
    db = 'A_ServiceMaster'

    #DB接続定義
    connection = MySQLdb.connect(
        host=dbHost,
        user=dbUser,
        passwd=dbPassword,
        db=dbUser)
    
    return connection

def getMasterDbCompanyInfo():
    targetGetSql = "SELECT companyId,companyName,dbUser,dbPassword,dbHost,mlHost,categorySaveDay "\
        "FROM G_Company "\
        "WHERE contractStatus <> 9"
    
    return targetGetSql

