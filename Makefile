
##@ Help

.DEFAULT_GOAL := help
help:  ## Display this help
	@awk 'BEGIN {FS = ":.*##"; printf "\nUsage:\n  make \033[32m<target>\033[0m\n"} /^[a-zA-Z_-]+:.*?##/ { printf "  \033[32m%-20s\033[0m %s\n", $$1, $$2 } /^##@/ { printf "\n\033[33m%s\033[0m\n", substr($$0, 5) } ' $(MAKEFILE_LIST)
##

##@ Test

test: ## Run phpunit tests
test:
	vendor/bin/phpunit -c tests/phpunit.xml.dist

##@ Release

release: ## Release a new version of app (.phar archives)
release:
	composer install --no-dev --profile
	composer dump-autoload --no-dev --profile
	php bin/build-phar
	chmod +x release/*

