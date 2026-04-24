export

# setup for docker-compose-ci build directory
# delete "build" directory to update docker-compose-ci

ifeq (,$(wildcard ./build/Makefile))
    $(shell git submodule update --init --remote)
endif

EXTENSION=qrlite

# docker images
MW_VERSION?=1.39
PHP_VERSION?=8.3
DB_TYPE?=mysql
DB_IMAGE?="mysql:8"

# extensions
# Enables installation of apt packages for gd extension
OS_PACKAGES?=zlib1g-dev libpng-dev

# Enables installation of gd extension
PHP_EXTENSIONS?=gd

# composer
# Enables "composer update" inside of extension
COMPOSER_EXT?=true

# nodejs
# Enables node.js related tests and "npm install"
NODE_JS?=true

# check for build dir and git submodule init if it does not exist
include build/Makefile

# QRLite has no JavaScript source files; npm-test-coverage is a no-op
.PHONY: npm-test-coverage
npm-test-coverage: .init
	@echo "Skipping npm-test-coverage: QRLite has no JavaScript source files"

.PHONY: composer-phan
composer-phan: .init ## Run Phan static analysis
	$(compose-exec-wiki) bash -c "cd $(EXTENSION_FOLDER) && composer phan $(COMPOSER_PARAMS)"

.PHONY: composer-phan-update-baseline
composer-phan-update-baseline: .init ## Re-generate baseline and fix indentation for PHPCS
	$(compose-exec-wiki) bash -c "cd $(EXTENSION_FOLDER) && composer phan -- --save-baseline=.phan/baseline.php"
	docker cp $(extension)-$(DB_TYPE)-wiki-1:$(EXTENSION_FOLDER)/.phan/baseline.php .phan/baseline.php
	unexpand --first-only -t 4 .phan/baseline.php > /tmp/baseline.php && mv /tmp/baseline.php .phan/baseline.php