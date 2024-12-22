# QATracker
QATracker is a tool to collect, store and render quality assurance indicators.  
For example, you can run on your project some static code analysis tools like phploc or phpcpd then collect some metrics
throught exported files. This specific collected data are stored in an other file that you can push to your repository if you want.

<div align="center">
    <img alt="QATracker report image" src="https://alxvgt.github.io/qatracker/images/qatracker.jpg" width="600" />
</div>

## Demo
You can view an example of a report here : [Demo](https://alxvgt.github.io/qatracker/demo/index.html)

## Requirements
You can view the requirements on packagist website : https://packagist.org/packages/alxvng/qatracker

## Installation
1. Install or replace the phar:
```bash
rm -f qatracker.phar && wget https://github.com/alxvgt/qa-tracker/raw/master/release/qatracker.phar
```
2. (option) Install the default configuration file :

```bash
wget --directory-prefix=.qatracker https://github.com/alxvgt/qa-tracker/raw/master/.qatracker.dist/config.yaml
```

If you prefer more stable version, checkout the last stable release at https://github.com/alxvgt/qatracker/releases

## Usage

How to run qatracker : 
```bash
php qatracker.phar
```

### Getting started
For a first usage se recommend you those steps :
1. Install QA tools : `php qatracker.phar install -n`
2. (option) Create or adapt the configuration to your project (paths, etc.) : `nano .qatracker/config.yaml`
1. Analyze, track then report your project : `php qatracker.phar history --step=XXX`
   - XXX is the number of days between each analysis based on your git history. It should be high (180+) if your project is old, low (7+) if your project is new.

## Configuration `config.yaml`

You need to put your own configuration in order to build your own indicators and reports.
The configuration is based on two objects :
 - **dataSerie** : this object enables you to pick/fetch data in any file you want. Each run of qatracker run each dataSerie provider and store in a file the currated data.
 - **chart** : this object associate a dataSerie to a chart in the final report

### Compose your configuration with imports

You can compose the main `config.yaml` with others config files by using `imports` directive.
The imported files are processed first then the content of `config.yaml` 

```yaml
imports:
    - { resource: config-phploc.yaml }
```  

## Docs & QA
Some informations about this tool are available here : [Docs](https://alxvgt.github.io/qatracker/)

## Contributing
You can contribute to this project by adding issue or pull request.