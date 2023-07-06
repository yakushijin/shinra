import pandas as pd
import numpy  as np
import spacy
from cassandra.cluster import Cluster
import logging
import socket
from json import loads

def Run():
    # ログレベルを DEBUG に変更
    # logging.basicConfig(filename='/var/www/logger.log', level=logging.DEBUG)

    host = "127.0.0.1"
    port = 8001

    with socket.socket(socket.AF_INET, socket.SOCK_STREAM) as s:
        s.bind((host, port))
        s.listen(1)
        while True:
            conn, addr = s.accept()
            with conn:
                while True:
                    byteStream = conn.recv(1024)
                    if not byteStream:
                        break
                    json = byteStream.decode(encoding='utf-8')
                    data = loads(json)
                    if data['apiId'] != '300':
                        conn.sendall("ng".encode(encoding='utf-8'))
                        break
                    
                    try:
                        cluster = Cluster([data['mlHost']])
                        session = cluster.connect(data['dbUser'])
                        
                        textData = data['textData']
                        targetTable = data['targetTable']
                        targetColumn = data['targetColumn']
                    except:
                        conn.sendall("datang".encode(encoding='utf-8'))
                        break

                    #取得したカテゴリとタスクのテキスト文を自然言語処理で解析
                    try:
                        nlp = spacy.load('ja_ginza')
                        doc = nlp(textData)
                    except:
                        conn.sendall("ginzadatang".encode(encoding='utf-8'))
                        break 

                    #自然言語の分割及び種別判定、必要なものを連想配列に格納
                    analysisWordList = []
                    try:
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
                    except:
                        conn.sendall("ginzang".encode(encoding='utf-8'))
                        break

                    #解析及びフィルタ済みの言語リストにて、親子関係で依存する言語を結合する
                    dependJoinWordListTmp = []    
                    joinWord = ""
                    #ワードの結合処理1。ワードを結合させる      
                    for i in analysisWordList:
                        #指定したタイプのワードの場合最初に前の結合対象ワードで結合する（1ループ目は必ず空、2ループ目以降に結合ワードが出る可能性がある）
                        # if i["wordType"] == 'PROPN' or i["wordType"] == 'NOUN' or i["wordType"] == 'NUM':
                        if  i["wordType"] == 'NUM':
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

                    wordArray =[]
                    try:
                        for i in dependJoinWordList:
                            selectSql = "select "+ targetColumn +" as id,score from "+ targetTable +" where word = '"+ i["word"] + "'"
                            # print(selectSql)

                            rows = session.execute(selectSql)
                            for row in rows:
                                wordArray.append(row)
                    except:
                        conn.sendall("cassandrang".encode(encoding='utf-8'))
                        break

                    useridArray = []
                    scoreArray = []
                    print(wordArray)
                    for row in wordArray:
                        useridArray.append(row.id)
                        scoreArray.append(row.score)

                    associativeArray = {targetColumn: useridArray, "score": scoreArray}
                    df = pd.DataFrame.from_dict(associativeArray)

                    groupedSum = df.groupby(targetColumn,as_index=False).sum()

                    maxScoreUser = groupedSum.sort_values(by='score',ascending=False)

                    resStart = '['
                    resEnd = ']'
                    res = ''
                    if maxScoreUser.values == []:
                        res = 'no'
                    else :
                        for data in maxScoreUser.head(3).values:
                            res = res + '{"'+targetColumn+'":' + str(list(data)[0]) + ',"score":'+str(list(data)[1]) + '},'
                                    
                    resdata = resStart + res[:-1] + resEnd
                    
                    conn.sendall(resdata.encode(encoding='utf-8'))





