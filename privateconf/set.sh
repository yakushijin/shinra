function setconf () {
source ../privateconf/set.conf

if [ ${1} = "dev" ]; then
domain=$devdomain
mymailaddress=$devmymailaddress
phpHostSshPort=$DevPhpHostSshPort
pythonHostSshPort=$DevPythonHostSshPort
phpHostIp=$devPhpHostIp
pythonHostIp=$devPythonHostIp
pythonBindIp=$devPythonBindIp
sshKey=$devSshKey

elif [ ${1} = "staging" ]; then
domain=$Stagindomain
mymailaddress=$Staginmymailaddress
phpHostSshPort=$StagingPhpHostSshPort
pythonHostSshPort=$StagingPythonHostSshPort
phpHostIp=$StagingPhpHostIp
pythonHostIp=$StagingPythonHostIp
pythonBindIp=$StagingPythonBindIp
sshKey=$StagingSshKey

elif [ ${1} = "prod" ]; then
domain=$Proddomain
mymailaddress=$Prodmymailaddress
phpHostSshPort=$ProdPhpHostSshPort
pythonHostSshPort=$ProdPythonHostSshPort
phpHostIp=$ProdPhpHostIp
pythonHostIp=$ProdPythonHostIp
pythonBindIp=$ProdPythonBindIp
sshKey=$ProdSshKey

elif [ ${1} = "my" ]; then
domain=$Mydomain
mymailaddress=$Mymymailaddress
phpHostSshPort=$MyPhpHostSshPort
pythonHostSshPort=$MyPythonHostSshPort
phpHostIp=$MyPhpHostIp
pythonHostIp=$MyPythonHostIp
pythonBindIp=$MyPythonBindIp
sshKey=$MySshKey

else
echo "error"
exit 1
fi
}
