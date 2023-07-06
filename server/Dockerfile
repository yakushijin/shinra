FROM centos:7
USER root

RUN rpm --import /etc/pki/rpm-gpg/RPM-GPG-KEY-CentOS-7 && \
    yum -y update && \
    yum clean all && \
    localedef -f UTF-8 -i ja_JP ja_JP.UTF-8 && \
    ln -sf /usr/share/zoneinfo/Asia/Tokyo /etc/localtime
ENV LANG="ja_JP UTF-8" \
    LANGUAGE="ja_JP:ja" \
    LC_ALL="ja_JP.UTF-8" \
    TZ="Asia/Tokyo"

RUN yum install -y net-tools dstat telnet traceroute tcpdump wget bind-utils nmap unzip

#-----------------------------------<nginx>-----------------------------------------
COPY ./setting/nginx.repo /etc/yum.repos.d/nginx.repo

RUN yum install -y nginx
RUN systemctl enable nginx.service

RUN mkdir -p /etc/nginx/ssl
RUN chmod 700 /etc/nginx/ssl

WORKDIR /etc/nginx/ssl

RUN openssl genrsa -out cert.key 2048
RUN openssl req -subj '/CN=common_name.example.com/O=ORGANIZATION/C=JP' -new -key cert.key > cert.csr
RUN openssl x509 -in cert.csr -days 3650 -req -signkey cert.key > cert.crt

WORKDIR /

COPY ./setting/shinra.conf /etc/nginx/conf.d/shinra.conf
COPY ./setting/nginx.conf /etc/nginx/nginx.conf

#-----------------------------------<php>-----------------------------------------

RUN yum install -y epel-release
RUN yum -y install http://rpms.famillecollet.com/enterprise/remi-release-7.rpm

RUN sed -i "/remi\/mirror/a enabled=1" /etc/yum.repos.d/remi.repo
RUN sed -i '/enabled=1/{n;d;}' /etc/yum.repos.d/remi.repo

RUN sed -i "/php73\/mirror/a enabled=1" /etc/yum.repos.d/remi-php73.repo
RUN sed -i '/enabled=1/{n;d;}' /etc/yum.repos.d/remi-php73.repo

RUN yum install -y php73-php-fpm php73-php-mysqlnd php-mysql php73-php-pecl-zip php73-php php73-php-devel php73-php-mbstring php73-php-pdo php73-php-gd php73-php-xml php73-php-mcrypt php73-php-opcache

ARG phppath="export PATH=/opt/remi/php73/root/usr/bin:/opt/remi/php73/root/usr/sbin\${PATH:+:\${PATH}}\nexport LD_LIBRARY_PATH=/opt/remi/php73/root/usr/lib64\${LD_LIBRARY_PATH:+:\${LD_LIBRARY_PATH}}\nexport MANPATH=/opt/remi/php73/root/usr/share/man:\${MANPATH}"
RUN sed -i -e "/User specific environment and startup program/a $phppath" /root/.bash_profile

RUN systemctl enable php73-php-fpm.service

RUN sed -i "s/user = apache/user = nginx/" /etc/opt/remi/php73/php-fpm.d/www.conf
RUN sed -i "s/group = apache/group = nginx/" /etc/opt/remi/php73/php-fpm.d/www.conf

RUN sed -i "s/pm.max_children = 50/pm.max_children = 200/" /etc/opt/remi/php73/php-fpm.d/www.conf
RUN sed -i "s/pm.start_servers = 5/pm.start_servers = 50/" /etc/opt/remi/php73/php-fpm.d/www.conf
RUN sed -i "s/pm.min_spare_servers = 5/pm.min_spare_servers = 20/" /etc/opt/remi/php73/php-fpm.d/www.conf
RUN sed -i "s/pm.max_spare_servers = 35/pm.max_spare_servers = 100/" /etc/opt/remi/php73/php-fpm.d/www.conf
RUN echo "listen.backlog = -1" >> /etc/opt/remi/php73/php-fpm.d/www.conf

RUN sed -i "s/expose_php = On/expose_php = Off/" /etc/opt/remi/php73/php.ini
RUN sed -i "s/ememory_limit = 128M/memory_limit = 1024M/" /etc/opt/remi/php73/php.ini
RUN sed -i "s/upload_max_filesize = 2M/upload_max_filesize = 128M/" /etc/opt/remi/php73/php.ini
RUN sed -i "s/post_max_size = 8M/post_max_size = 128M/" /etc/opt/remi/php73/php.ini

RUN mkdir /var/run/php-fpm/
RUN echo "d /var/run/php-fpm 0755 nginx nginx -" > /usr/lib/tmpfiles.d/php-fpm.conf 
RUN sed -i "s/listen = 127.0.0.1:9000/listen = \/var\/run\/php-fpm\/php-fpm.sock/g" /etc/opt/remi/php73/php-fpm.d/www.conf
RUN sed -i "s/;listen.owner = nobody/listen.owner = nginx/g" /etc/opt/remi/php73/php-fpm.d/www.conf
RUN sed -i "s/;listen.group = nobody/listen.group = nginx/g" /etc/opt/remi/php73/php-fpm.d/www.conf
RUN sed -i "s/;listen.mode = 066/listen.mode = 066/g" /etc/opt/remi/php73/php-fpm.d/www.conf

RUN ln -s /usr/bin/php73 /usr/bin/php
RUN curl -sS https://getcomposer.org/installer | php
RUN mv composer.phar /usr/local/bin/composer

#-----------------------------------<python>-----------------------------------------

RUN yum install -y gcc

RUN yum -y install https://repo.ius.io/ius-release-el7.rpm
RUN yum install -y python36u python36u-libs python36u-devel python36u-pip 
RUN yum -y install https://dev.mysql.com/get/mysql80-community-release-el7-1.noarch.rpm
RUN yum install -y mysql-community-devel

RUN pip3.6 install -U pip setuptools wheel
RUN pip3.6 install pandas numpy Cython
RUN pip3.6 install -U ginza
RUN pip3.6 install cassandra-driver
RUN pip3.6 install mysqlclient

COPY ./setting/python_server.service /etc/systemd/system/python_server.service

RUN systemctl enable python_server

RUN mkdir -p /var/www/python
RUN chmod 755 /var/www/python

#-----------------------------------<cassandra>-----------------------------------------

COPY ./setting/cassandra.repo /etc/yum.repos.d/cassandra.repo

RUN yum -y install cassandra

RUN yum downgrade -y http://vault.centos.org/7.6.1810/updates/x86_64/Packages/systemd-219-62.el7_6.9.x86_64.rpm http://vault.centos.org/7.6.1810/updates/x86_64/Packages/systemd-libs-219-62.el7_6.9.x86_64.rpm http://vault.centos.org/7.6.1810/updates/x86_64/Packages/systemd-sysv-219-62.el7_6.9.x86_64.rpm
RUN echo "exclude=systemd*" >> /etc/yum.conf

RUN systemctl enable cassandra

# #-----------------------------------<mysql>-----------------------------------------
RUN yum -y install https://dev.mysql.com/get/mysql80-community-release-el7-2.noarch.rpm
RUN yum install -y mysql-community-server mysql-community-devel

RUN sed -i -e "/# default-authentication-plugin=mysql_native_password/a default-authentication-plugin=mysql_native_password" /etc/my.cnf
RUN echo "max_connections        = 100" >> /etc/my.cnf

RUN systemctl enable mysqld.service

COPY ./setting/dbsetup.sql /tmp/dbsetup.sql
COPY ./setting/dbsetup.sh /tmp/dbsetup.sh
RUN chmod 700 /tmp/dbsetup.sh


#-----------------------------------<end>-----------------------------------------
RUN history -c
WORKDIR /var/www/laravel
