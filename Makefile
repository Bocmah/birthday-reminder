.PHONY: server-start docker-up docker-down watch migrate-generate migrate
.DEFAULT_GOAL := help

help: ## Show this help
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_-]+:.*?## / {printf "  \033[36m%-15s\033[0m %s\n", $$1, $$2}' $(MAKEFILE_LIST)

docker-up-build:
	docker-compose up -d --build

docker-up:
	docker-compose up -d

docker-down:
	docker-compose down