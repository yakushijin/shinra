echo "start"

#リリース対象判定
source ../privateconf/set.sh
setconf ${1}

sleep 1

scp -P $pythonHostSshPort -i $sshKey ../src/python/* root@$pythonHostIp:/var/www/python/
echo "scpDone"

sleep 1

#デプロイ実行
ssh -p $pythonHostSshPort -i $sshKey root@$pythonHostIp "sed -i -e 's/127.0.0.1/$pythonBindIp/g' /var/www/python/s_get_allNewWord.py"
ssh -p $pythonHostSshPort -i $sshKey root@$pythonHostIp "sed -i -e 's/127.0.0.1/$pythonBindIp/g' /var/www/python/s_get_userToWord.py"
ssh -p $pythonHostSshPort -i $sshKey root@$pythonHostIp "sed -i -e 's/127.0.0.1/$pythonBindIp/g' /var/www/python/s_get_wordToUser.py"

ssh -p $pythonHostSshPort -i $sshKey root@$pythonHostIp "chmod 500 /var/www/python/*;systemctl restart python_server"

source ../privateconf/${1}/.env
ssh -p $pythonHostSshPort -i $sshKey root@$pythonHostIp "sed -i -e 's/localhost/$DB_HOST/g' /var/www/python/masterDbConnect.py"
ssh -p $pythonHostSshPort -i $sshKey root@$pythonHostIp "echo '$DB_PASSWORD' > /var/www/python/pass;chmod 400 /var/www/python/pass" 

echo "deployDone"
