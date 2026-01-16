env_file = "./.env"
docker_compose_file = ./docker-compose.yml
init: 
		docker compose --file $(docker_compose_file) --env-file $(env_file) exec php bash -c "composer install && php artisan key:generate && php artisan migrate"
clear:
		docker compose --file $(docker_compose_file) --env-file $(env_file) exec php bash -c "php artisan optimize:clear" 
node:
		docker compose --file $(docker_compose_file) --env-file $(env_file) run --rm node sh -c "npm i && npm run build" 