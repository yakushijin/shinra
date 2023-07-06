#webサーバディレクトリ名
wwwhome=laravel

rm -fR /var/www/$wwwhome/

cd /var/www/
mv -f /root/laravel.tar.gz /var/www/
tar zxf laravel.tar.gz

mv -f /root/.env /var/www/$wwwhome/
mv -f /root/ga.php /var/www/$wwwhome/resources/views/layouts/

chown -R nginx:nginx /var/www/$wwwhome/
chmod -R 755 /var/www/$wwwhome/

chmod -R 755 /var/www/$wwwhome/storage
chmod -R 755 /var/www/$wwwhome/bootstrap/cache

cd $wwwhome/
composer dump-autoload

# systemctl restart php73-php-fpm
# systemctl restart nginx

