export

# setup for docker-compose-ci build directory
# delete "build" directory to update docker-compose-ci

ifeq (,$(wildcard ./build/Makefile))
    $(shell git submodule update --init --remote)
endif

EXTENSION=qrlite

# docker images
MW_VERSION?=1.35
PHP_VERSION?=7.4
DB_TYPE?=mysql
DB_IMAGE?="mysql:5.7"

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
# NODE_JS?=true
# Note: activate NODE_JS together with dropping MW 1.35 (requires Node >= 14)

# check for build dir and git submodule init if it does not exist
include build/Makefile

.PHONY: composer-phan
composer-phan: .init ## Run Phan static analysis
	$(compose-exec-wiki) bash -c "cd $(EXTENSION_FOLDER) && composer phan $(COMPOSER_PARAMS)"

.PHONY: composer-phan-update-baseline
composer-phan-update-baseline: .init ## Re-generate baseline and fix indentation for PHPCS
	$(compose-exec-wiki) bash -c "cd $(EXTENSION_FOLDER) && composer phan -- --save-baseline=.phan/baseline.php"
	docker cp $(extension)-$(DB_TYPE)-wiki-1:$(EXTENSION_FOLDER)/.phan/baseline.php .phan/baseline.php
	unexpand --first-only -t 4 .phan/baseline.php > /tmp/baseline.php && mv /tmp/baseline.php .phan/baseline.php