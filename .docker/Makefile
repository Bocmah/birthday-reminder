DOCKER_ROOT_DIR = $(PROJECT_ROOT_DIR)/.docker

# Calc image revisions
GET_PATH_SHA = $(DOCKER_ROOT_DIR)/bin/get_path_sha

APP_IMAGE_SHA = $(shell $(GET_PATH_SHA) $(DOCKER_ROOT_DIR)/php)

build-app-dev:
	$(call build_image,app-dev,--target=dev)

build-app-test:
	$(call build_image,app-test,--target=test)

build-app-prod:
	$(call build_image,app,--target=prod)

### build_image,name,args
define build_image
	@docker build \
	--tag $(PROJECT_NAME)/$(1):$(APP_IMAGE_SHA) \
	--tag $(PROJECT_NAME)/$(1):latest \
	--file $(DOCKER_ROOT_DIR)/php/Dockerfile \
	$(PROJECT_ROOT_DIR) \
	$(2)
endef
