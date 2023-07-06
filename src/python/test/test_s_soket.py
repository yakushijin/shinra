import socket
import logging
import spacy
from json import loads

host = "127.0.0.1"
port = 8009

with socket.socket(socket.AF_INET, socket.SOCK_STREAM) as s:
    s.bind((host, port))
    s.listen(1)
    while True:
        conn, addr = s.accept()
        with conn:
            while True:
                data = conn.recv(1024)
                if not data:
                    break
                json = data.decode(encoding='utf-8')

                test = loads(json)
                print(test)

                print(test['data'])

                conn.sendall("ok".encode(encoding='utf-8'))


