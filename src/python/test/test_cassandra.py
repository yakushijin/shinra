import pandas as pd
import numpy  as np
import spacy
from cassandra.cluster import Cluster
import logging

import sys
args = sys.argv
inputText = args[1]

# ログレベルを DEBUG に変更
logging.basicConfig(filename='/var/www/logger.log', level=logging.DEBUG)

cluster = Cluster(['localhost'])
session = cluster.connect('test')

#スタブ。これはフロントから受け取る想定
word = inputText

#指定したワードの依存情報を取得
sql = "select word,dependWord,dependedOnWord from test.wordset where userid = 1 and word = '"+word +"'"
rows = session.execute(sql)

#依存しているものがあるものを除外
wordarray = []
for row in rows:
    if not row.dependword:
        wordarray.append(row)

#依存関連を再帰的に取得（呼び出し先再帰処理メソッド）
def wordget(word,wordarray,count):
    filterword = word
    for i in wordarray:
        sql = "select word,dependWord,dependedOnWord from test.wordset where userid = 1 and word = '"+i.dependedonword +"'"
        rows = session.execute(sql)
        for row in rows:
            if row.dependword == filterword:               
                info = [
                    count,
                    row.word
                ]
                wordarray2.append(info)
                wordget(word = row.word,wordarray = rows,count=count)

# print(wordarray)

#依存関連を再帰的に取得
wordarray2 = []
count = 0
for row in wordarray:
    count += 1 
    wordarray3 = []
    wordarray3.append(row)
    # print(wordarray3)
    wordget(word = word,wordarray = wordarray3,count=count)

print(wordarray2)
