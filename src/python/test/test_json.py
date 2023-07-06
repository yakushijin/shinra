import socket
import logging
import spacy
import json

test2 = '{"data":"abcああああああ"}'
test3 = json.loads(test2)

print(test3['data'])

for x in test3:
    print(x)