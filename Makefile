PROJECT_NAME=qa-tracker

###### Paths

PATH_QA_ROOT = docs/qa
PATH_QA_PHPUNIT = phpunit
PATH_QA_MAKEFILE_DIR = qa

###### Commands : Docker

test-docker = $(shell command -v docker || '')
test-docker-compose = $(if $(shell command -v docker-compose || exit 0), docker-compose -f docker/docker-compose.yml, )
test-docker-compose-exec = $(if $(shell command -v docker-compose || exit 0), ${CMD_DOCKER_COMPOSE} exec -T app, ${CMD_DOCKER_COMPOSE})

CMD_SUDO = sudo
CMD_DOCKER 					= ${CMD_SUDO} $(call test-docker)
CMD_DOCKER_COMPOSE 			= ${CMD_SUDO} $(call test-docker-compose)
CMD_EXEC = $(call test-docker-compose-exec)

###### Commands : Others

CMD_PHP ?= ${CMD_EXEC} php
CMD_COMPOSER ?= ${CMD_EXEC} composer
CMD_CHMOD ?= ${CMD_EXEC} chmod
CMD_PWD ?= ${CMD_EXEC} pwd

CMD_PHPUNIT ?= ${CMD_PHP} vendor/bin/phpunit -c tests/phpunit.xml.dist
CMD_PHPCSFIX ?= ${CMD_PHP} vendor/bin/php-cs-fixer fix -v

###### START

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
	${CMD_PHPUNIT}

##@ QA tools

cs: ## Check code style
cs: install
	${CMD_PHPCSFIX} --dry-run --using-cache=no

cs-fix: ## Fix code style
cs-fix: install
	${CMD_PHPCSFIX} --using-cache=no
	@printf "\n\033[32m--------------------------------------------------------------------------\033[0m"
	@printf "\n\033[32mCheck after fix\033[0m"
	@printf "\n\033[32m--------------------------------------------------------------------------\033[0m"
	@printf "\n"
	make cs

coverage: ## Run test coverage
coverage: install
	$(eval outputDir=${PATH_QA_ROOT}/${PATH_QA_PHPUNIT}/coverage-html)
	mkdir -p ${outputDir}
	${CMD_PHPUNIT} --coverage-html=${outputDir}

qa-report: ## Run all the qa tools
qa-report:
	make --directory ${PATH_QA_MAKEFILE_DIR} install-all
	make --directory ${PATH_QA_MAKEFILE_DIR} run-all
	cp -Rf /tmp/qa-logs/* ${PATH_QA_ROOT}/.
	make coverage
	make cs

##@ Release

release: ## Release a new version of app (.phar archives)
release: cs-fix qa-report
	${CMD_COMPOSER} install --no-dev --profile
	${CMD_COMPOSER} dump-autoload --no-dev --profile
	${CMD_PHP} bin/build-phar
	${CMD_CHMOD} +x release/*

###### END