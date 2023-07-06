import MySQLdb
 
connection = MySQLdb.connect(
    host='localhost',
    user='dbUser1',
    passwd='dbPassword1!',
    db='dbUser1')
cursor = connection.cursor()

cursor.execute("SELECT userId,categoryName,categoryId FROM T_Category where learnedFlg = 0")
rows = cursor.fetchall()


for row in rows:
    cursor.execute("update T_Category set learnedFlg = 1 where categoryId = " + str(row[1]))


for row in rows:
    userId = row[0]
    getText = row[1]
    print(userId)

connection.commit()
 
connection.close()
