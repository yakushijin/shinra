pass=`grep "A temporary password" /var/log/mysqld.log | awk '{print $13}'`
mysql -u root -p$pass --connect-expired-password -e "alter user 'root'@'localhost' identified by 'developRoot123?'; flush privileges;"
mysql -u root -p'developRoot123?' --connect-expired-password < /tmp/dbsetup.sql