MAKEFLAGS += --no-print-directory
MAKEFLAGS += --environment-overrides

INSTALL_DIR=/tmp
LOG_DIR=/tmp/qa-logs

ANALYZED_DIR=/var/www/qa-tracker/src

##@ Help

.DEFAULT_GOAL := help
help:  ## Display this help
	@awk 'BEGIN {FS = ":.*##"; printf "\nUsage:\n  make \033[32m<target>\033[0m\n"} /^[a-zA-Z_-]+:.*?##/ { printf "  \033[32m%-20s\033[0m %s\n", $$1, $$2 } /^##@/ { printf "\n\033[33m%s\033[0m\n", substr($$0, 5) } ' $(MAKEFILE_LIST)

##@ Installation

install-all: ## Install all php code tools
install-all: ${INSTALL_DIR}/phpcpd.phar ${INSTALL_DIR}/phploc.phar ${INSTALL_DIR}/phpmd.phar ${INSTALL_DIR}/phpmetrics.phar

${INSTALL_DIR}/phploc.phar:
	@rm -f ${INSTALL_DIR}/phploc.phar
	@wget --show-progress -q -P ${INSTALL_DIR} https://phar.phpunit.de/phploc.phar
	@chmod +x ${INSTALL_DIR}/phploc.phar
	@printf "\033[92m > Complete: tool installed at \"${INSTALL_DIR}/phploc.phar\"\n\033[0m"

${INSTALL_DIR}/phpcpd.phar:
	@rm -f ${INSTALL_DIR}/phpcpd.phar
	@wget --show-progress -q -P ${INSTALL_DIR} https://phar.phpunit.de/phpcpd.phar
	@chmod +x ${INSTALL_DIR}/phpcpd.phar
	@printf "\033[92m > Complete: tool installed at \"${INSTALL_DIR}/phpcpd.phar\"\n\033[0m"

${INSTALL_DIR}/phpmd.phar:
	@rm -f ${INSTALL_DIR}/phpmd.phar
	@wget --show-progress -q -P ${INSTALL_DIR} https://phpmd.org/static/latest/phpmd.phar
	@chmod +x ${INSTALL_DIR}/phpmd.phar
	@printf "\033[92m > Complete: tool installed at \"${INSTALL_DIR}/phpmd.phar\"\n\033[0m"

${INSTALL_DIR}/phpmetrics.phar:
	@rm -f ${INSTALL_DIR}/phpmetrics.phar
	@wget --show-progress -q -P ${INSTALL_DIR} https://github.com/phpmetrics/PhpMetrics/raw/master/releases/phpmetrics.phar
	@chmod +x ${INSTALL_DIR}/phpmetrics.phar
	@printf "\033[92m > Complete: tool installed at \"${INSTALL_DIR}/phpmetrics.phar\"\n\033[0m"

##@ Run

run-all: ## Run all php code tools
run-all: run-phploc run-phpcpd run-phpmd run-phpmetrics

run-phploc: ## Run phploc tool
run-phploc:
	$(eval phar_name=phploc)
	$(eval tool_log_dir=${LOG_DIR}/${phar_name})
	$(eval tool_log_file=${tool_log_dir}/log.xml)
	@mkdir -p $(tool_log_dir)
	@rm -rf $(tool_log_file)
	php ${INSTALL_DIR}/$(phar_name).phar -q --log-xml=$(tool_log_file) ${ANALYZED_DIR}
	@printf "\033[92m > Complete: $(phar_name) log generated at \"$(tool_log_file)\"\n\033[0m"

run-phpcpd: ## Run phpcpd tool
run-phpcpd:
	$(eval phar_name=phpcpd)
	$(eval tool_log_dir=${LOG_DIR}/${phar_name})
	$(eval tool_log_file=${tool_log_dir}/log.xml)
	@mkdir -p $(tool_log_dir)
	@rm -rf $(tool_log_file)
	php ${INSTALL_DIR}/$(phar_name).phar -q --log-pmd=$(tool_log_file) ${ANALYZED_DIR}
	@printf "\033[92m > Complete: $(phar_name) log generated at \"$(tool_log_file)\"\n\033[0m"

run-phpmd: ## Run phpmd tool
run-phpmd:
	$(eval phar_name=phpmd)
	$(eval tool_log_dir=${LOG_DIR}/${phar_name})
	$(eval tool_log_file=${tool_log_dir}/index.html)
	@mkdir -p $(tool_log_dir)
	@rm -rf $(tool_log_file)
	php ${INSTALL_DIR}/$(phar_name).phar ${ANALYZED_DIR} html cleancode,codesize,controversial,design,naming,unusedcode --reportfile $(tool_log_file) || echo ''
	@printf "\033[92m > Complete: $(phar_name) log generated at \"$(tool_log_file)\"\n\033[0m"

run-phpmetrics: ## Run phpmetrics tool
run-phpmetrics:
	$(eval phar_name=phpmetrics)
	$(eval tool_log_dir=${LOG_DIR}/${phar_name})
	$(eval tool_log_file=${tool_log_dir}/index.html)
	@mkdir -p $(tool_log_dir)
	@rm -rf $(tool_log_file)
	php ${INSTALL_DIR}/$(phar_name).phar ${ANALYZED_DIR} --report-html=$(tool_log_dir) --git
	@printf "\033[92m > Complete: $(phar_name) log generated at \"$(tool_log_file)\"\n\033[0m"