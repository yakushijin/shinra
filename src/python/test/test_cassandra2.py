import pandas as pd
import numpy  as np
import spacy
from cassandra.cluster import Cluster
import logging

import sys
args = sys.argv
inputText = args[1]

#依存関連を再帰的に取得（呼び出し先再帰処理メソッド）
def wordget(word,wordarray,count):
    filterword = word
    for i in wordarray:
        sql = "select word,dependWord,dependedOnWord,score from test.wordset where userid = 1 and word = '"+i.dependedonword +"'"
        rows = session.execute(sql)
        for row in rows:
            if row.dependword == filterword:               
                info = [
                    count,
                    row.word,
                    row.score
                ]
                wordarray2.append(info)
                wordget(word = row.word,wordarray = rows,count=count)


# ログレベルを DEBUG に変更
logging.basicConfig(filename='/var/www/logger.log', level=logging.DEBUG)

cluster = Cluster(['localhost'])
session = cluster.connect('test')

#スタブ。これはフロントから受け取る想定
userId = inputText

sql = "select word,score from test.wordset where userId = "+ userId +" and dependWord = ''"

rows = session.execute(sql)

wordArray = []
scoreArray = []

for row in rows:
    wordArray.append(row.word)
    scoreArray.append(row.score)

associativeArray = {"word": wordArray, "score": scoreArray}
df = pd.DataFrame.from_dict(associativeArray)

wordData = df.groupby("word",as_index=False).sum()

# print(wordData)


resStart = '['
resEnd = ']'
res = ''
if wordData.values == []:
    res = 'no'
else :
    try:
        for data in wordData.head(500).values:
            res = res + '{"word":"' + str(list(data)[0]) + '","score":'+str(list(data)[1]) + ',"dependedOnWord":{'


            #指定したワードの依存情報を取得
            sql = "select word,dependWord,dependedOnWord from test.wordset where userid = 1 and word = '"+list(data)[0] +"'"
            rows = session.execute(sql)

            #依存しているものがあるものを除外
            wordarray = []
            for row in rows:
                if not row.dependword:
                    wordarray.append(row)

            #依存関連を再帰的に取得
            wordarray2 = []
            count = 0
            for row in wordarray:
                count += 1 
                wordarray3 = []
                wordarray3.append(row)
                wordarray2 = []
                wordget(word = list(data)[0],wordarray = wordarray3,count=count)
                if not wordarray:
                    break
                else:
                    if not wordarray2:
                        res = res + ',' 
                        break
                    else:
                        key = 1
                        res = res + '"' + str(key) + '":[' 
                        for row in wordarray2:
                            res = res + '{"word":"'+str(list(row)[1])+'","score":'+str(list(row)[2])+'},'
                        res = res[:-1]
                        res = res + '],' 
            res = res[:-1]
            res = res + '}'
            res = res + '},'
        
          
    except:
        pass
resdata = resStart + res[:-1] + resEnd
print(resdata)                
