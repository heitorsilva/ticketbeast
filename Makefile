db-console:
	@docker-compose exec mysql mysql -u root -p

migrate:
	@docker-compose exec app php artisan migrate

rollback:
	@docker-compose exec app php artisan migrate:rollback

test:
	@docker-compose exec app ./vendor/bin/phpunit $(filter-out $@,$(MAKECMDGOALS))

update:
	@docker-compose exec app composer self-update
	@docker-compose exec app php artisan cache:clear
	@docker-compose exec app php artisan clear-compiled
	@docker-compose exec app composer update -vvv
