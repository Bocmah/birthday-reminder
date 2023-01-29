PROJECT_NAME     ?= birthday-reminder
PROJECT_ROOT_DIR := $(realpath $(dir $(abspath $(firstword $(MAKEFILE_LIST)))))

# Image version and current commit hash:
export VERSION   ?= $(shell git describe --all --dirty --always | sed -E 's/[a-z]+\///' | sed -E 's/\//-/')
export GIT_SHA   := $(shell git rev-parse HEAD)
export TIMESTAMP := $(shell date +"%Y%m%d%H%M%S")

export DOCKER_BUILDKIT := 1

include .docker/Makefile

.DEFAULT_GOAL := help
APP_SERVICE = app

help: ## Show this help
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_-]+:.*?## / {printf "  \033[36m%-15s\033[0m %s\n", $$1, $$2}' $(MAKEFILE_LIST)

up: ## Start services
	docker-compose up -d

down: ## Stop services
	docker-compose down

restart: ## Restart services
	docker-compose down && docker-compose up -d

ps: ## Dump running services
	@docker-compose ps

logs: ## Show app logs
	@docker-compose logs app

logs-tail: ## Follow app logs
	@docker-compose logs -f app

cli: ## Jump to app service shell
	@docker-compose exec $(APP_SERVICE) sh

phpunit: ## Run tests
	@docker-compose exec -T $(APP_SERVICE) composer phpunit

phpcs: ## Run PHP Code Sniffer across all project files
	@docker-compose exec -T $(APP_SERVICE) composer phpcs

php-cs-fixer: ## Run PHP CS Fixer across all project files
	@docker-compose exec -T $(APP_SERVICE) composer php-cs-fixer

infection: ## Run infection across all project files
	@docker-compose exec -T $(APP_SERVICE) composer infection

psalm: ## Run psalm across all project files
	@docker-compose exec -T $(APP_SERVICE) composer psalm

migrate-generate: ## Generate template migration
	@docker-compose exec $(APP_SERVICE) composer migrate-generate

migrate: ## Run migrations
	docker-compose exec $(APP_SERVICE) composer migrate

composer-update:
	@docker-compose exec $(APP_SERVICE) composer update

composer: ## Run arbitrary composer command
	@docker-composer exec $(APP_SERVICE) composer $(COMPOSER_FLAGS)
