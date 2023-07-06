import pandas as pd
import numpy  as np
import spacy
from cassandra.cluster import Cluster
import logging
import socket
from json import loads



def Run():
    #依存関連を再帰的に取得（呼び出し先再帰処理メソッド）
    def wordget(word,wordarray,count,targetTable,targetColumn,targetId):
        filterword = word
        for i in wordarray:
            try:
                sql = "select word,dependWord,dependedOnWord,score from "+ targetTable +" where "+ targetColumn +" = "+ targetId +" and word = '"+i.dependedonword +"'"
                rows = session.execute(sql)
                for row in rows:
                    # if row.dependword == filterword:               
                    info = [
                        count,
                        row.word,
                        row.score
                    ]
                    dependedonWordArray.append(info)
                    wordget(word = row.word,wordarray = rows,count=2,targetTable=targetTable,targetColumn=targetColumn,targetId=targetId)
            except:
                continue


    # ログレベルを DEBUG に変更
    # logging.basicConfig(filename='/var/www/logger.log', level=logging.DEBUG)

    host = "127.0.0.1"
    port = 8003

    with socket.socket(socket.AF_INET, socket.SOCK_STREAM) as s:
        s.bind((host, port))
        s.listen(1)
        while True:
            conn, addr = s.accept()
            with conn:
                while True:
                    try:
                        byteStream = conn.recv(8196)
                        if not byteStream:
                            conn.sendall("socketerror".encode(encoding='utf-8'))
                            break
                    except:
                        conn.sendall("socketerror".encode(encoding='utf-8'))
                        break
                    try:
                        json = byteStream.decode(encoding='utf-8')
                        data = loads(json)
                    except:
                        conn.sendall("jsonerror".encode(encoding='utf-8'))
                        break
                    if data['apiId'] != '200':
                        conn.sendall("apierror".encode(encoding='utf-8'))
                        break
 
                    try:
                        cluster = Cluster([data['mlHost']])
                        session = cluster.connect(data['dbUser'])
                    except:
                        conn.sendall("nosqlconnecterror".encode(encoding='utf-8'))
                        break

                    try:
                        targetTable = data['targetTable']
                        targetId = data['targetId']
                        targetColumn = data['targetColumn']
                        sql = "select word,score from "+ targetTable +" where "+ targetColumn +" = "+ targetId +" and dependWord = ''"
                    except:
                        conn.sendall("dataseterror".encode(encoding='utf-8'))
                        break
                    
                    try:
                        rows = session.execute(sql)

                        wordArray = []
                        scoreArray = []

                        for row in rows:
                            wordArray.append(row.word)
                            scoreArray.append(row.score)
                    except:
                        conn.sendall("cassandrageterror".encode(encoding='utf-8'))
                        break

                    try:
                        associativeArray = {"word": wordArray, "score": scoreArray}
                        df = pd.DataFrame.from_dict(associativeArray)

                        wordData = df.groupby("word",as_index=False).sum()
                        scoreSortWordData = wordData.sort_values(by=["score"], ascending=True)
                        limitWordData = scoreSortWordData.head(100)
                        sortWordData = limitWordData.sort_values(by=["word"], ascending=True)

                    except:
                        conn.sendall("dataformaterror".encode(encoding='utf-8'))
                        break

                    resStart = '['
                    resEnd = ']'
                    res = ''
                    if sortWordData.values == []:
                        res = 'no'
                    else :
                        for data in sortWordData.values:
                            res = res + '{"word":"' + str(list(data)[0]) + '","score":'+str(list(data)[1]) + ',"dependedOnWord":{'

                            #指定したワードの依存情報を取得
                            try:
                                sql = "select word,dependWord,dependedOnWord from "+ targetTable +" where "+ targetColumn +" = "+ targetId +" and word = '"+list(data)[0] +"'"
                                rows = session.execute(sql)
                            except:
                                continue

                            #依存しているものがあるものを除外（1階層目レコードに絞り込み）
                            topWordArray = []
                            for row in rows:
                                if not row.dependword:
                                    topWordArray.append(row)

                            #依存関連を再帰的に取得
                            #------------------------------------------------------------------------
                            #依存されるワード格納用
                            dependedonWordArray = []
                            count = 0
                            key = 0
                            try:
                                #フィルタしたワードリストを展開し、依存されているワードがあれば格納していく
                                for row in topWordArray:
                                    count += 1 
                                    key += 1 
                                    #対象レコードのワード格納用配列
                                    mainWordArray = []
                                    mainWordArray.append(row)

                                    #これから抽出する依存されるワード格納用配列初期化
                                    dependedonWordArray = []

                                    #関数内で依存されるワードを格納する
                                    wordget(word = list(data)[0],wordarray = mainWordArray,count=count,targetTable=targetTable,targetColumn=targetColumn,targetId=targetId)
                                    if not topWordArray:
                                        break
                                    else:
                                        if not dependedonWordArray:
                                            res = res + ',' 
                                            #ループ終了後のjson成形状態　'{"word":"ワード","score":スコア,"dependedOnWord":{},'
                                            break
                                        else:
                                            res = res + '"datakey' + str(key) + '":[' 
                                            #この時点のjson成形状態　'{"word":"ワード","score":スコア,"dependedOnWord":{"datakey' + str(key) + '":['
                                            for row in dependedonWordArray:
                                                res = res + '{"word":"'+str(list(row)[1])+'","score":'+str(list(row)[2])+'},'
                                            #この時点のjson成形状態　'{"word":"ワード","score":スコア,"dependedOnWord":{"datakey' + str(key) + '":[{"word":"依存ワード","score":依存ワードスコア},{"word":"依存ワード","score":依存ワードスコア},'　※×n
                                            res = res[:-1]
                                            res = res + '],'
                                            #この時点のjson成形状態　'{"word":"ワード","score":スコア,"dependedOnWord":{"datakey' + str(key) + '":[{"word":"依存ワード","score":依存ワードスコア},{"word":"依存ワード","score":依存ワードスコア}],
                                            #ループ終了後のjson成形状態　'{"word":"ワード","score":スコア,"dependedOnWord":{"datakey' + str(key) + '":[{"word":"依存ワード","score":依存ワードスコア},{"word":"依存ワード","score":依存ワードスコア}]}},
                            except:
                                pass
                            res = res[:-1]
                            res = res + '}'
                            res = res + '},'   


                    resdata = resStart + res[:-1] + resEnd
                    
                    conn.sendall(resdata.encode(encoding='utf-8'))





