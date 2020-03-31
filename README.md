# QATracker
QATracker is a tool to collect, store and render quality assurance indicators.  
For example, you can run on your project some static code analysis tools like phploc or phpcpd then collect som metrics
throught exported files. This specific collected data are stored in an other file that you can push to your repository if you want.

## Requirements
* php >= 7.4.0
* ext-json  
* ext-xml   
* lib-pcre  

## Installation
Install the phar :
`wget https://github.com/alxvgt/qa-tracker/raw/master/qatracker.phar --no-cache`

## Usage
Launch the command line
`php qatracker.phar --help`

