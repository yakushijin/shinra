from cassandra.cluster import Cluster
import logging

# ログレベルを DEBUG に変更
# logging.basicConfig(filename='/var/www/logger.log', level=logging.DEBUG)

import sys
args = sys.argv
dbHost = args[1]
mlUser = args[2]

cluster = Cluster([dbHost])
session = cluster.connect(mlUser)

session.execute("truncate userWordset")
session.execute("truncate groupWordSet")
session.execute("truncate tabWordSet")
session.execute("truncate newSummaryWordSet")
