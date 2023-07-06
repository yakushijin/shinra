
CREATE DATABASE A_ServiceMaster character set utf8 collate utf8_bin;

CREATE USER A_ServiceMaster@localhost IDENTIFIED WITH mysql_native_password BY 'developApp123?';
GRANT ALL ON A_ServiceMaster.* TO A_ServiceMaster@localhost;

FLUSH PRIVILEGES;
exit
