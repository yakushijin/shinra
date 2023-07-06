import logging
import spacy
from spacy import displacy
import pandas as pd
import numpy  as np
from cassandra.cluster import Cluster
import logging


import sys
args = sys.argv
inputText = args[1]

nlp = spacy.load('ja_ginza')

#スタブ
userId = 1

doc = nlp(inputText)    

cluster = Cluster(['localhost'])
session = cluster.connect('test')

# displacy.serve(doc, style="dep")

#自然言語の分割及び種別判定、必要なものを連想配列に格納
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
# print(analysisWordList)

#解析及びフィルタ済みの言語リストにて、親子関係で依存する言語を結合する
dependJoinWordList = []    
joinWord = ""      
for i in analysisWordList:
    i["word"] = joinWord + i["word"]
    joinWord = "" 
    if i["dependType"] == 'compound':
        joinWord = i["word"]
        # print(joinWord)
    dependJoinWordList.append(i)

for i in dependJoinWordList:
    if i["dependType"] == 'compound':
        dependJoinWordList.remove(i)

# print(dependJoinWordList)

#結合済みのリストにて、言語ごとの依存関係を解析し、リストに格納する
dependAddWordList = []  
dependWord = ""
for i in dependJoinWordList:
    i["depend"] = dependWord
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
    dependAddWordList.append(i)

print(dependAddWordList)


#登録処理
for i in dependAddWordList:
    selectSql = "select score from test.wordset where userId = "+str(userId)+" and word = '" + i["word"] + "' and dependWord = '" + i["depend"] + "' and dependedOnWord = '" + i["dependedOn"] + "' " 
    # print(selectSql)

    rows = session.execute(selectSql)
    score = 1

    if rows == []:
        updateScore = score
    else:
        updateScore = rows[0].score + score


    updateSql = "INSERT INTO test.wordset (userId, word, dependWord,dependedOnWord,score, createDate) VALUES ( "+str(userId)+",'"+i["word"]+"','"+i["depend"]+"','"+i["dependedOn"]+"', "+str(updateScore)+", toDate(now()))"
    # print(updateSql)
    session.execute(updateSql)
