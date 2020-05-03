SHELL := /bin/bash

REPOSITORY_URL=https://github.com/alxvgt/qatracker.git
GIT_REFS= \
["20200402120000"]=v0.1.0 \
["20200403100000"]=v0.2.0 \
["20200403190000"]=v0.3.0 \
["20200404190000"]=v0.4.0 \
["20200418180000"]=v0.5.0 \
["20200503120000"]=master

WORKING_DIR=/tmp/temp-git

QA_MAKEFILE_DIR=$(shell pwd)/../qa
QA_LOG_DIR=/tmp/qa/logs
QATRACKER_CONFIG_DIR=$(shell pwd)/../.qatracker

###### START

##@ Help

.DEFAULT_GOAL := help
help:  ## Display this help
	@awk 'BEGIN {FS = ":.*##"; printf "\nUsage:\n  make \033[32m<target>\033[0m\n"} /^[a-zA-Z_-]+:.*?##/ { printf "  \033[32m%-20s\033[0m %s\n", $$1, $$2 } /^##@/ { printf "\n\033[33m%s\033[0m\n", substr($$0, 5) } ' $(MAKEFILE_LIST)
##

##@ Git

qa-history: ## Execute operations
qa-history:
	printf "\n\033[32m Cleaning working directories, files, etc. \033[0m\n"
	rm -rf ${WORKING_DIR}
	rm -rf ${QA_LOG_DIR}
	rm -rf ${QATRACKER_CONFIG_DIR}/generated
	make -e --directory ${QA_MAKEFILE_DIR} clean-qatracker

	printf "\n\033[32m Installing tools... \033[0m\n"
	make -e --directory ${QA_MAKEFILE_DIR} install-all

	printf "\n\033[32m Clonning repository... \033[0m\n"
	git clone --depth=1 ${REPOSITORY_URL} ${WORKING_DIR}
	cd ${WORKING_DIR}

	declare -A REFS=( ${GIT_REFS} ) ; \
	for date in $${!REFS[@]} ; do \
	  	ref=$${REFS[$${date}]} ; \
		printf "\n\033[32m Processing $${ref}... \033[0m" ; \
		cd ${WORKING_DIR} ; \
		\
  		printf "\n\033[32m --> fetch... \033[0m\n" ; \
  		git fetch --depth=1 origin $${ref}:$${ref} ; \
  		\
  		printf "\n\033[32m --> checkout... \033[0m\n" ; \
  		git checkout $${ref} ; \
  		\
  		printf "\n\033[32m --> running code analysis... \033[0m\n" ; \
  		PROJECT_DIR=${WORKING_DIR} LOG_DIR=${QA_LOG_DIR} make -e --directory ${QA_MAKEFILE_DIR} run-all ; \
		\
  		printf "\n\033[32m --> running qatracker ($${date} > $${ref})... \033[0m\n" ; \
  		QATRACKER_CONFIG_DIR=${QATRACKER_CONFIG_DIR} QATRACKER_BASE_DIR=${QA_LOG_DIR} QATRACKER_DATE=$${date} make -e --directory ${QA_MAKEFILE_DIR} run-qatracker ; \
	done