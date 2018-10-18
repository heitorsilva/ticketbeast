db-console:
	@docker-compose exec mysql mysql -u root -p

migrate:
	@docker-compose exec app php artisan migrate

rollback:
	@docker-compose exec app php artisan migrate:rollback

test:
	@docker-compose exec app ./vendor/bin/phpunit

update:
	@php artisan cache:clear
	@php artisan clear-compiled
	@composer update
