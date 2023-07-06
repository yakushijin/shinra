import logging
import spacy
from spacy import displacy

import sys
args = sys.argv
inputText = args[1]

nlp = spacy.load('ja_ginza')

doc = nlp(inputText)    

displacy.serve(doc, style="dep")

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
dependJoinWordListTmp = []    
joinWord = ""
#ワードの結合処理1。ワードを結合させる      
for i in analysisWordList:
    #指定したタイプのワードの場合最初に前の結合対象ワードで結合する（1ループ目は必ず空、2ループ目以降に結合ワードが出る可能性がある）
    # if i["wordType"] == 'PROPN' or i["wordType"] == 'NOUN' or i["wordType"] == 'NUM':
    if i["wordType"] ==  i["wordType"] == 'NUM':
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

print(dependJoinWordList)

#結合済みのリストにて、言語ごとの依存関係を解析し、リストに格納する
dependAddWordList = []  
dependWord = ""
for i in dependJoinWordList:
    #依存されるワードを格納（1ループ目は必ず空、2ループ目以降に依存されるワードが出る可能性がある）
    i["depend"] = dependWord
    print(i)
    if i["dependType"] == 'compound':
        for j in dependJoinWordList:
            i["dependedOn"] = j["word"]
            dependWord = i["word"]

    if i["dependType"] == 'nmod':
        for j in dependJoinWordList:
            if j["dependType"] == 'obj' or j["dependType"] == 'obl':
                i["dependedOn"] = j["word"]
                dependWord = i["word"]
    if i["dependType"] == 'obj':
        for j in dependJoinWordList:
            if j["dependType"] == 'ROOT':
                i["dependedOn"] = j["word"] 
                dependWord = i["word"]
    if i["dependType"] == 'obl':
        for j in dependJoinWordList:
            if j["dependType"] == 'obl':
                i["dependedOn"] = j["word"] 
                dependWord = i["word"]
    if i["dependType"] == 'iobj':
        for j in dependJoinWordList:
            if j["wordType"] == 'VERB':
                i["dependedOn"] = j["word"] 
                dependWord = i["word"]                 
    dependAddWordList.append(i)

# print(dependAddWordList)