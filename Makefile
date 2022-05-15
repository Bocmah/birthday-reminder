PROJECT_NAME     ?= vkbd
PROJECT_ROOT_DIR := $(realpath $(dir $(abspath $(firstword $(MAKEFILE_LIST)))))

# Image version and current commit hash:
export VERSION   ?= $(shell git describe --all --dirty --always | sed -E 's/[a-z]+\///' | sed -E 's/\//-/')
export GIT_SHA   := $(shell git rev-parse HEAD)
export TIMESTAMP := $(shell date +"%Y%m%d%H%M%S")

export DOCKER_BUILDKIT := 1

include .docker/Makefile

.DEFAULT_GOAL := help
PHP_SERVICE = php

help: ## Show this help
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_-]+:.*?## / {printf "  \033[36m%-15s\033[0m %s\n", $$1, $$2}' $(MAKEFILE_LIST)

up-build: ## Build and start services
	docker-compose up -d --build

up: ## Start services
	docker-compose up -d

down: ## Stop services
	docker-compose down

shell: ## Jump to PHP service shell
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
