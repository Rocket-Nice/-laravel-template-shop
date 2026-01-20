env_file = "./.env"
docker_compose_file = ./docker-compose.yml
init: 
		docker compose --file $(docker_compose_file) --env-file $(env_file) exec php bash -c "composer install && php artisan key:generate && php artisan migrate"
clear:
		docker compose --file $(docker_compose_file) --env-file $(env_file) exec php bash -c "php artisan optimize:clear" 
node:
		docker compose --file $(docker_compose_file) --env-file $(env_file) run --rm node sh -c "npm i && npm run build"

test-php:
		docker compose --file $(docker_compose_file) --env-file $(env_file) exec php bash -c "./vendor/bin/phpunit"

test-node:
		docker compose --file $(docker_compose_file) --env-file $(env_file) run --rm node sh -c "npm test"

lint-node:
		docker compose --file $(docker_compose_file) --env-file $(env_file) run --rm node sh -c "npm install -y && npx eslint resources/js/ --ext .js,.vue"

lint-php:
		docker compose --file $(docker_compose_file) --env-file $(env_file) exec php bash -c "./vendor/bin/pint app/ config/ database/ routes/ --dirty"
