DOCKER_ROOT_DIR = $(PROJECT_ROOT_DIR)/.docker

# Calc image revisions
GET_PATH_SHA = $(DOCKER_ROOT_DIR)/bin/get_path_sha

PHP_BASE_IMAGE_SHA = $(shell $(GET_PATH_SHA) $(DOCKER_ROOT_DIR)/php-base)
PHP_DEV_IMAGE_SHA  = $(shell $(GET_PATH_SHA) $(DOCKER_ROOT_DIR)/php-dev)

build-php-base: ## Build PHP base image
	docker build \
		-t $(PROJECT_NAME)/php-base:$(PHP_BASE_IMAGE_SHA) \
		-t $(PROJECT_NAME)/php-base:latest \
		-f $(DOCKER_ROOT_DIR)/php-base/Dockerfile \
		$(PROJECT_ROOT_DIR)

build-php-dev: build-php-base ## Build PHP dev image
	docker build \
		--build-arg PHP_BASE_IMAGE=$(PROJECT_NAME)/php-base:$(PHP_BASE_IMAGE_SHA) \
		-t $(PROJECT_NAME)/php-dev:$(PHP_DEV_IMAGE_SHA) \
		-t $(PROJECT_NAME)/php-dev:latest \
		-f $(DOCKER_ROOT_DIR)/php-dev/Dockerfile \
		$(PROJECT_ROOT_DIR)
