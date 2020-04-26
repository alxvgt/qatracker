PROJECT_NAME=qa-tracker

CMD_SUDO = sudo

###### Docker

test-docker = $(shell command -v docker || '')
test-docker-compose = $(if $(shell command -v docker-compose || exit 0), docker-compose -f docker/docker-compose.yml, )
test-docker-compose-exec = $(if $(shell command -v docker-compose || exit 0), ${CMD_DOCKER_COMPOSE} exec -T app, ${CMD_DOCKER_COMPOSE})

CMD_DOCKER 					= ${CMD_SUDO} $(call test-docker)
CMD_DOCKER_COMPOSE 			= ${CMD_SUDO} $(call test-docker-compose)
CMD_EXEC = $(call test-docker-compose-exec)

###### Others

CMD_PHP ?= ${CMD_EXEC} php
CMD_COMPOSER ?= ${CMD_EXEC} composer
CMD_CHMOD ?= ${CMD_EXEC} chmod
CMD_PWD ?= ${CMD_EXEC} pwd

CMD_PHPUNIT ?= ${CMD_EXEC} vendor/bin/phpunit

##@ Help

.DEFAULT_GOAL := help
help:  ## Display this help
	@awk 'BEGIN {FS = ":.*##"; printf "\nUsage:\n  make \033[32m<target>\033[0m\n"} /^[a-zA-Z_-]+:.*?##/ { printf "  \033[32m%-20s\033[0m %s\n", $$1, $$2 } /^##@/ { printf "\n\033[33m%s\033[0m\n", substr($$0, 5) } ' $(MAKEFILE_LIST)
##

##@ Project

install: ## Install project
install: composer.json
	${CMD_COMPOSER} install

##@ Test

test: ## Run phpunit tests
test: install
	${CMD_PHPUNIT} -c tests/phpunit.xml.dist

##@ Release

release: ## Release a new version of app (.phar archives)
release:
	${CMD_COMPOSER} install --no-dev --profile
	${CMD_COMPOSER} dump-autoload --no-dev --profile
	${CMD_PHP} bin/build-phar
	${CMD_CHMOD} +x release/*
