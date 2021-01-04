.PHONY: help docker-up-build docker-up docker-down \
		docker-shell composer-update phpunit phpcs \
		php-cs-fixer infection psalm migrate-generate \
		migrate
.DEFAULT_GOAL := help
PHP_SERVICE = php

help: ## Show this help
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_-]+:.*?## / {printf "  \033[36m%-15s\033[0m %s\n", $$1, $$2}' $(MAKEFILE_LIST)

docker-up-build: ## Build and start services
	docker-compose up -d --build

docker-up: ## Start services
	docker-compose up -d

docker-down: ## Stop services
	docker-compose down

docker-shell: ## Jump to PHP service shell
	@docker-compose exec $(PHP_SERVICE) bash

composer-update: ## Update all composer dependencies
	@docker-compose exec $(PHP_SERVICE) composer update

phpunit: ## Run tests
	@docker-compose exec -T $(PHP_SERVICE) composer phpunit

phpcs: ## Run PHP Code Sniffer across all project files
	@docker-compose exec -T $(PHP_SERVICE) composer phpcs

php-cs-fixer: ## Run PHP CS Fixer across all project files
	@docker-compose exec -T $(PHP_SERVICE) composer php-cs-fixer

infection: ## Run infection across all project files
	@docker-compose exec -T $(PHP_SERVICE) composer infection

psalm: ## Run psalm across all project files
	@docker-compose exec -T $(PHP_SERVICE) composer psalm

migrate-generate: ## Generate template migration
	@docker-compose exec $(PHP_SERVICE) composer migrate-generate

migrate: ## Run migrations
	@docker-compose exec $(PHP_SERVICE) composer migrate
