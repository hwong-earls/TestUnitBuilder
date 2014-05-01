Earls TestUnit Builder
======================

It is a Symfony bundle which implement a console command for auto create **TestUnits** for your project.
This tool explore ./src and create ./tests folder in where all the **TestUnit** will be alocated follow the same folder structure from the host bundle.

##Installation

composer.json:

"require": {
	"earls/TestBuilderBundle": "dev-master"
},

"repositories": [
	{
    	"type": "vcs",
        "url": "https://github.com/hwong-earls/TestUnitBuilder"
    }
]

Execute composer.phar update

##Configuration

*config.yml* inside *earls/TestBuilderBundle/Earls/TestBuilderBundle/Resources/config*

Sections:
**startpoint:**
   Location of the source, start point for scan the project.   

**excludeDir:**
   List of directories for ignore.

**excludeFile:**
   List of files for ignore. Use *regular expresion*.

Example:

`startpoint:` /src
`excludeDir:`
    - Tests
    - DependencyInjection
    - Command
    - RhinoReportBundle
`excludeFile:`
    - .*Bundle\.php
    - .*Test\.php
    - .*\.phar
    - FieldFilterType.php
    - NumberFilterType.php