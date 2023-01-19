EXTENSION := QRLite

MW_VERSION ?= 1.35

EXTENSION_FOLDER := /var/www/html/extensions/$(EXTENSION)
IMAGE_NAME := $(shell echo $(EXTENSION) | tr A-Z a-z}):test-$(MW_VERSION)-$(SMW_VERSION)
PWD := $(shell bash -c "pwd -W 2>/dev/null || pwd")# this way it works on Windows and Linux
DOCKER_RUN_ARGS := --rm -v $(PWD)/coverage:$(EXTENSION_FOLDER)/coverage -w $(EXTENSION_FOLDER) $(IMAGE_NAME)
docker_run := docker run $(DOCKER_RUN_ARGS)

.PHONY: all
all:

# ======== CI ========

.PHONY: ci
ci: build test

.PHONY: ci-coverage
ci-coverage: build test-coverage

.PHONY: build
build:
	docker build --tag $(IMAGE_NAME) \
		--build-arg=MW_VERSION=$(MW_VERSION) \
		.

.PHONY: test
test: composer-test npm-test

.PHONY: test-coverage
test-coverage: composer-test-coverage

.PHONY: composer-test
composer-test:
	$(docker_run) composer test

.PHONY: npm-test
npm-test:
	$(docker_run) npm run test

.PHONY: composer-test-coverage
composer-test-coverage:
	$(docker_run) composer test-coverage

.PHONY: bash
bash:
	docker run -it -v $(PWD):/src $(DOCKER_RUN_ARGS) bash

.PHONY: dev-bash
dev-bash:
	docker run -it --rm \
		-v $(PWD):$(EXTENSION_FOLDER) -v $(EXTENSION_FOLDER)/node_modules -v $(EXTENSION_FOLDER)/vendor \
		-w $(EXTENSION_FOLDER) $(IMAGE_NAME) bash
