![CI](https://github.com/alxvgt/qatracker/workflows/CI/badge.svg)

# QATracker
QATracker is a tool to collect, store and render quality assurance indicators.  
For example, you can run on your project some static code analysis tools like phploc or phpcpd then collect som metrics
throught exported files. This specific collected data are stored in an other file that you can push to your repository if you want.

<div align="center">
    <img alt="QATracker report image" src="https://alxvgt.github.io/qatracker/images/qatracker.jpg" width="600" />
</div>

## Demo
You can view an example of a report here : [Demo](https://alxvgt.github.io/qatracker/)

## Requirements
You can view the requirements on packagist website : https://packagist.org/packages/alxvng/qatracker

## Installation
If necessary, remove the previous installation :

```bash
rm qatracker.phar;
```

Install the phar :
```bash
wget https://github.com/alxvgt/qa-tracker/raw/master/qatracker.phar
```

## Usage

How to run qatracker : 
```bash
php qatracker.phar
```

### First time
1. Run your favorite QA tools and produce log files
1. Run qatracker, if no configuration file is detected, the tool generate a sample for you.
1. Adapt the generated sample file to your metrics logs, then run again the tool.
1. An html page report will be generated, you can open it in your favorite browser. You can see the report but empty charts (not enough values collected).

_Note :_ at least **two collects** of metrics **are needed** to display an historization chart.

### Periodic usage
1. Run your favorite QA tools and produce log files, again
1. Run again qatracker, it collect new metrics from the same qa tools log files paths that you have already configured then, it generate a new report with old and new values.
1. Now you can see the history of your favorite metrics. Enjoy !

## Configuration `config.yaml`

You need to put your own configuration in order to build your own indicators and reports.
The configuration is based on two objects :
 - **dataSerie** : this object enables you to pick/fetch data in any file you want. Each run of qatracker run each dataSerie provider and store in a file the currated data.
 - **chart** : this object associate a dataSerie to a chart in the final report
 
```yaml
qatracker:
    dataSeries:
        [list of your data series]
    charts:
        [list of your charts]
```

**Example:**
You can see the default config file at `.qatracker.dist/config.yaml`

### dataSeries

You need to follow this structure and replace variable between brackets :

```yaml
[id]:
    class: [provider class]
    arguments:
        - [path to a qa tool log file]
        - [expression to retrieve data in the file (xpath, jsonpath, etc.)]
```

Available provider classes:
- _Alxvng\QATracker\DataProvider\XPathProvider_ : pick a unique data in a xml file 
- _Alxvng\QATracker\DataProvider\XPathSumProvider_ : pick many data in a xml file and reduce it with a sum
- _Alxvng\QATracker\DataProvider\XPathCountProvider_ : count nodes retrieved by the expresion in the xml file
- _Alxvng\QATracker\DataProvider\XPathAverageProvider_ : pick many data in a xml file and compute the average
- _Alxvng\QATracker\DataProvider\JsonPathProvider_ : pick a unique data in a json file
- _Alxvng\QATracker\DataProvider\JsonPathSumProvider_ : pick many data in a json file and reduce it with a sum
- _Alxvng\QATracker\DataProvider\JsonPathCountProvider_ : count nodes retrieved by the expresion in the json file
- _Alxvng\QATracker\DataProvider\JsonPathAverageProvider_ : pick many data in a json file and compute the average

**Example:** 
```yaml
total-duplicated-lines:
    class: Alxvng\QATracker\DataProvider\XpathSumProvider
    arguments:
        - '/tmp/qa-logs/phpcpd/log.xml'
        - '/pmd-cpd/duplication/@lines'
```

### charts

You need to follow this structure and replace variable between brackets :

```yaml
[id]:
    type: [graph type class]
    dataSeries:
        - [data serie id]
    graphSettings:
        [graph settings]
```

Graph type classes :
You can find all available graph options in the library documentation : https://www.goat1000.com/svggraph.php#graph-types

Data serie id :
You should refer to a data serie defined in the previous section.

Graph settings :
You can find all available graph options in the library documentation : https://goat1000.com/svggraph-options.php

**Example:**
```yaml
total-duplicated-lines:
    type: Goat1000\SVGGraph\LineGraph
    dataSeries:
        - 'total-duplicated-lines'
    graphSettings:
        graph_title: 'Totla duplicated lines'
```

### Compose your configuration with imports

You can compose the main `config.yaml` with others config files by using `imports` directive.
The imported files are processed first then the content of `config.yaml` 

```yaml
imports:
    - { resource: config-phploc.yaml }
```  

## Contributing
You can contribute to this project by adding issue or pull request.
In order to start the project, you can follow some instructions below :

### Requirements
* docker >= 19.03.5
* docker-compose >= 1.25.0

### Installation

First, fork this repository.
Then, follow some instructions below.

```bash
git clone <your-fork-repository-url>
cd qatracker/docker
make start
make connect
```
You are now connected to the container and you can start working on the project

