MAKEFLAGS += --no-print-directory
MAKEFLAGS += --environment-overrides

INSTALL_DIR=/tmp
LOG_DIR=/tmp/qa/logs

QATRACKER_DIR=$(shell pwd)/../.qatracker

PROJECT_DIR=/var/www/qa-tracker

PATH_QA_ROOT=$(shell pwd)/../docs/qa
PATH_QA_PHPUNIT=${PATH_QA_ROOT}/phpunit

##@ Help

.DEFAULT_GOAL := help
help:  ## Display this help
	@awk 'BEGIN {FS = ":.*##"; printf "\nUsage:\n  make \033[32m<target>\033[0m\n"} /^[a-zA-Z_-]+:.*?##/ { printf "  \033[32m%-20s\033[0m %s\n", $$1, $$2 } /^##@/ { printf "\n\033[33m%s\033[0m\n", substr($$0, 5) } ' $(MAKEFILE_LIST)

##@ Installation

install-all: ## Install all php code tools
install-all: ${INSTALL_DIR}/phpcpd.phar ${INSTALL_DIR}/phploc.phar ${INSTALL_DIR}/phpmd.phar ${INSTALL_DIR}/phpmetrics.phar ${INSTALL_DIR}/qatracker.phar

clean-qatracker: ## Remove qatracker bin
clean-qatracker:
	rm -f ${INSTALL_DIR}/qatracker.phar

clean: ## Remove all installed tools and clear the log directory
clean: clean-qatracker
	rm -f ${INSTALL_DIR}/phploc.phar
	rm -f ${INSTALL_DIR}/phpcpd.phar
	rm -f ${INSTALL_DIR}/phpmd.phar
	rm -f ${INSTALL_DIR}/phpmetrics.phar
	rm -rf ${LOG_DIR}


${INSTALL_DIR}/phploc.phar:
	wget --show-progress -q -P ${INSTALL_DIR} https://phar.phpunit.de/phploc.phar
	chmod +x ${INSTALL_DIR}/phploc.phar
	printf "\033[92m > Complete: tool installed at \"${INSTALL_DIR}/phploc.phar\"\n\033[0m"

${INSTALL_DIR}/phpcpd.phar:
	wget --show-progress -q -P ${INSTALL_DIR} https://phar.phpunit.de/phpcpd.phar
	chmod +x ${INSTALL_DIR}/phpcpd.phar
	printf "\033[92m > Complete: tool installed at \"${INSTALL_DIR}/phpcpd.phar\"\n\033[0m"

${INSTALL_DIR}/phpmd.phar:
	wget --show-progress -q -P ${INSTALL_DIR} https://phpmd.org/static/latest/phpmd.phar
	chmod +x ${INSTALL_DIR}/phpmd.phar
	printf "\033[92m > Complete: tool installed at \"${INSTALL_DIR}/phpmd.phar\"\n\033[0m"

${INSTALL_DIR}/phpmetrics.phar:
	wget --show-progress -q -P ${INSTALL_DIR} https://github.com/phpmetrics/PhpMetrics/raw/master/releases/phpmetrics.phar
	chmod +x ${INSTALL_DIR}/phpmetrics.phar
	printf "\033[92m > Complete: tool installed at \"${INSTALL_DIR}/phpmetrics.phar\"\n\033[0m"

${INSTALL_DIR}/qatracker.phar:
	wget --show-progress -q -P ${INSTALL_DIR} https://github.com/alxvgt/qa-tracker/raw/master/release/qatracker.phar
	chmod +x ${INSTALL_DIR}/qatracker.phar
	printf "\033[92m > Complete: tool installed at \"${INSTALL_DIR}/qatracker.phar\"\n\033[0m"

##@ Run

run-all: ## Run all php code tools
run-all: run-phploc run-phpcpd run-phpmd run-phpmetrics

run-phploc: ## Run phploc tool
run-phploc:
	$(eval phar_name=phploc)
	$(eval tool_log_dir=${LOG_DIR}/${phar_name})
	mkdir -p $(tool_log_dir)

	$(eval tool_log_file_xml=${tool_log_dir}/log.xml)
	rm -rf $(tool_log_file_xml)
	$(eval tool_log_file_csv=${tool_log_dir}/log.csv)
	rm -rf $(tool_log_file_csv)
	$(eval tool_log_file_json=${tool_log_dir}/log.json)
	rm -rf $(tool_log_file_json)

	php ${INSTALL_DIR}/$(phar_name).phar --quiet --log-xml=$(tool_log_file_xml) --log-csv=$(tool_log_file_csv) --log-json=$(tool_log_file_json) ${PROJECT_DIR}/src ${PROJECT_DIR}/tests  || echo 'OK'
	printf "\033[92m > Complete: $(phar_name) log generated at \"$(tool_log_file_xml)\"\n\033[0m"

run-phpcpd: ## Run phpcpd tool
run-phpcpd:
	$(eval phar_name=phpcpd)
	$(eval tool_log_dir=${LOG_DIR}/${phar_name})
	mkdir -p $(tool_log_dir)

	$(eval tool_log_file=${tool_log_dir}/log.xml)
	rm -rf $(tool_log_file)

	php ${INSTALL_DIR}/$(phar_name).phar --min-tokens=20 --quiet --log-pmd=$(tool_log_file) ${PROJECT_DIR}/src ${PROJECT_DIR}/tests || echo 'OK'
	printf "\033[92m > Complete: $(phar_name) log generated at \"$(tool_log_file)\"\n\033[0m"

run-phpmd: ## Run phpmd tool
run-phpmd:
	$(eval phar_name=phpmd)
	$(eval tool_log_dir=${LOG_DIR}/${phar_name})
	mkdir -p $(tool_log_dir)

	$(eval tool_log_file=${tool_log_dir}/index.html)
	rm -rf $(tool_log_file)
	php ${INSTALL_DIR}/$(phar_name).phar ${PROJECT_DIR}/src,${PROJECT_DIR}/tests html cleancode,codesize,controversial,design,naming,unusedcode --reportfile $(tool_log_file) || printf ''
	printf "\033[92m > Complete: $(phar_name) log generated at \"$(tool_log_file)\"\n\033[0m"

	$(eval tool_log_file=${tool_log_dir}/log-ansi.txt)
	rm -rf $(tool_log_file)
	php ${INSTALL_DIR}/$(phar_name).phar ${PROJECT_DIR}/src,${PROJECT_DIR}/tests ansi cleancode,codesize,controversial,design,naming,unusedcode --reportfile $(tool_log_file) || printf ''
	printf "\033[92m > Complete: $(phar_name) log generated at \"$(tool_log_file)\"\n\033[0m"

	$(eval tool_log_file=${tool_log_dir}/log.json)
	rm -rf $(tool_log_file)
	php ${INSTALL_DIR}/$(phar_name).phar ${PROJECT_DIR}/src,${PROJECT_DIR}/tests json cleancode,codesize,controversial,design,naming,unusedcode --reportfile $(tool_log_file) || printf ''
	printf "\033[92m > Complete: $(phar_name) log generated at \"$(tool_log_file)\"\n\033[0m"

	$(eval tool_log_file=${tool_log_dir}/log.txt)
	rm -rf $(tool_log_file)
	php ${INSTALL_DIR}/$(phar_name).phar ${PROJECT_DIR}/src,${PROJECT_DIR}/tests text cleancode,codesize,controversial,design,naming,unusedcode --reportfile $(tool_log_file) || printf ''
	printf "\033[92m > Complete: $(phar_name) log generated at \"$(tool_log_file)\"\n\033[0m"

	$(eval tool_log_file=${tool_log_dir}/log.xml)
	rm -rf $(tool_log_file)
	php ${INSTALL_DIR}/$(phar_name).phar ${PROJECT_DIR}/src,${PROJECT_DIR}/tests xml cleancode,codesize,controversial,design,naming,unusedcode --reportfile $(tool_log_file) || printf ''
	printf "\033[92m > Complete: $(phar_name) log generated at \"$(tool_log_file)\"\n\033[0m"

run-phpmetrics: ## Run phpmetrics tool
run-phpmetrics:
	$(eval phar_name=phpmetrics)
	$(eval tool_log_dir=${LOG_DIR}/${phar_name})
	mkdir -p $(tool_log_dir)

	$(eval tool_log_dir_html=${tool_log_dir}/html)
	rm -rf $(tool_log_dir_html)
	$(eval tool_log_file_json=${tool_log_dir}/log.json)
	rm -rf $(tool_log_file_json)
	$(eval tool_log_file_csv=${tool_log_dir}/log.csv)
	rm -rf $(tool_log_file_csv)
	$(eval tool_log_file_xml=${tool_log_dir}/log.xml)
	rm -rf $(tool_log_file_xml)

	php ${INSTALL_DIR}/$(phar_name).phar ${PROJECT_DIR}/src,${PROJECT_DIR}/tests,${PROJECT_DIR}/composer.json,${PROJECT_DIR}/composer.lock --quiet --report-json=$(tool_log_file_json) --report-csv=$(tool_log_file_csv) --report-violations=$(tool_log_file_xml) --report-html=$(tool_log_dir_html) --junit=${PATH_QA_PHPUNIT}/junit.xml --git
	printf "\033[92m > Complete: $(phar_name) log generated at \"$(tool_log_dir_html)\"\n\033[0m"

run-qatracker: ## Run qatracker tool
run-qatracker:
	$(eval phar_name=qatracker)
	php ${INSTALL_DIR}/$(phar_name).phar --base-dir=${QATRACKER_BASE_DIR} --config-dir=${QATRACKER_CONFIG_DIR} --date=${QATRACKER_DATE}
	printf "\033[92m > Complete: $(phar_name) report generated under \"${QATRACKER_DIR}\"\n\033[0m"