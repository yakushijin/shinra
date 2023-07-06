echo "start"

#リリース対象判定
source ../privateconf/set.sh
setconf ${1}

#ビルド実行
./build.sh ${1}
echo "buidlDone"

sleep 1

#ビルド済み圧縮ファイルとデプロイ用シェルをサーバへ送信
scp -P $phpHostSshPort -i $sshKey ../compile_src/laravel.tar.gz root@$phpHostIp:/root/
scp -P $phpHostSshPort -i $sshKey ../privateconf/${1}/.env root@$phpHostIp:/root/
scp -P $phpHostSshPort -i $sshKey ../privateconf/${1}/ga.php root@$phpHostIp:/root/
sleep 1
scp -P $phpHostSshPort -i $sshKey deploy.sh root@$phpHostIp:/root/
echo "scpDone"

sleep 1

#デプロイ実行
ssh -p $phpHostSshPort -i $sshKey root@$phpHostIp "chmod 700 /root/deploy.sh;/root/deploy.sh"
echo "deployDone"
