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
    port = 8004

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
                    if data['apiId'] != '500':
                        conn.sendall("ng".encode(encoding='utf-8'))
                        break
                    try:
                        cluster = Cluster([data['mlHost']])
                        session = cluster.connect(data['dbUser'])
                    except:
                        conn.sendall("datang".encode(encoding='utf-8'))
                        break

                    try:                                    
                        sql = "select word,score from newSummaryWordSet"
                    except:
                        conn.sendall("cassandrang".encode(encoding='utf-8'))
                        break

                    rows = session.execute(sql)

                    wordArray = []
                    scoreArray = []

                    for row in rows:
                        wordArray.append(row.word)
                        scoreArray.append(row.score)

                    associativeArray = {"word": wordArray, "score": scoreArray}
                    df = pd.DataFrame.from_dict(associativeArray)

                    wordData = df.groupby("word",as_index=False).sum()

                    resStart = '['
                    resEnd = ']'
                    res = ''
                    if wordData.values == []:
                        res = 'no'
                    else :
                        try:
                            for data in wordData.head(500).values:
                                res = res + '{"word":"' + str(list(data)[0]) + '","score":'+str(list(data)[1]) + '},'
                        except:
                            pass

                    resdata = resStart + res[:-1] + resEnd

                    print(resdata)

                    conn.sendall(resdata.encode(encoding='utf-8'))





