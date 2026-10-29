.PHONY: help up down restart build logs ps bash migrate seed test clean

help: ## Show this help message
	@echo "Usage: make [target]"
	@echo ""
	@echo "Targets:"
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "  \033[36m%-15s\033[0m %s\n", $$1, $$2}'

up: ## Start all Docker containers in background
	docker compose up -d

down: ## Stop all Docker containers
	docker compose down

restart: ## Restart all Docker containers
	docker compose down && docker compose up -d

build: ## Build or rebuild Docker containers
	docker compose up -d --build

logs: ## Tail logs for all containers (or specify s=<service_name>)
	docker compose logs -f $(s)

ps: ## List running containers and status
	docker compose ps

bash: ## Open bash inside the Laravel app container
	docker compose exec app bash

migrate: ## Run Laravel migrations
	docker compose exec app php artisan migrate

seed: ## Run Laravel database seeders
	docker compose exec app php artisan db:seed

migrate-fresh: ## Drop all tables and re-run all migrations with seeders
	docker compose exec app php artisan migrate:fresh --seed

test: ## Run test suite
	docker compose exec app php artisan test

composer-install: ## Run composer install inside container
	docker compose exec app composer install

key-generate: ## Generate app key inside container
	docker compose exec app php artisan key:generate

clean: ## Stop containers and remove persistent volumes
	docker compose down -v

