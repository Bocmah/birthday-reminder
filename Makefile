.PHONY: docker-up-build docker-up docker-down
.DEFAULT_GOAL := help
PHP_SERVICE = php

help: ## Show this help
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_-]+:.*?## / {printf "  \033[36m%-15s\033[0m %s\n", $$1, $$2}' $(MAKEFILE_LIST)

docker-up-build:
	docker-compose up -d --build

docker-up:
	docker-compose up -d

docker-down:
	docker-compose down

docker-shell:
	@docker-compose exec $(PHP_SERVICE) bash