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

### Initilization
1. Run your favorite QA tools and produce log files
1. Run qatracker, if no configuration file is detected, the tool generate a sample for you.
1. Adapt the generated sample file to your metrics logs, then run again the tool.
1. An html page report will be generated, you can open it in your favorite browser. You can see the report but empty charts (not enough values collected).

_Note :_ at least **two collects** of metrics **are needed** to display an historization chart.

### Periodic usage
1. Run your favorite QA tools and produce log files, again
1. Run again qatracker, it collect new metrics from the same qa tools log files paths that you have already configured then, it generate a new report with old and new values.
1. Now you can see the history of your favorite metrics. Enjoy !

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

