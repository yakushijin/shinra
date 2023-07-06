init:
	docker exec -it shinra_run /tmp/dbsetup.sh
	docker exec -it shinra_run composer install
	docker exec -it shinra_run composer dump-autoload
	docker exec -it shinra_run chmod -R 777 /var/www/laravel/storage/
	docker exec -it shinra_run cp -p .env.example .env
	docker exec -it shinra_run php artisan config:clear
	docker exec -it shinra_run php artisan key:generate
	docker exec -it shinra_run php artisan migrate
	docker exec -it shinra_run php artisan db:seed
