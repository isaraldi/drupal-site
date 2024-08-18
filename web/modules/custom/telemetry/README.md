## Telemetry Module

This telemetry module is a functional test both to validate technical knowledge and to evaluate the possibilities of features offered by the most current versions of Drupal.

The primary use case for this module is:

- Receive information about execution, installation, etc. quickly and clearly, without the need to access complex panels and systems, using everyday tools;
- Carefully monitor processes that require attention - such as those that may eventually generate additional costs, such as installation, build and/or inadvertent use;
- Store messages sent via telemetry locally and/or remotely;

## REQUIREMENTS

- Drupal 10 or newer;
- DDEV 1.23.4 or newer;
- Composer version 2.7.7 or newer;
- PHP version 8.3.10 or newer;

## INSTALLATION

Install as you would normally install a contributed Drupal module.
See: https://www.drupal.org/node/895232 for further information.

## CONFIGURATION

- Configuration step #1
- Configuration step #2
- Configuration step #3

## MAINTAINERS

- John Murowaniecki 🜏 ( _jmurowaniecki_ · aka `0xD3C0de`);


## Contribute

### Getting started

First download and configure the project following the steps below:

```bash
git clone git@github.com:jmurowaniecki/zoocha.git drupal-site
# to acquire the project
```


Inside project directory execute the following commands:
```bash
ddev composer install
# to install project dependencies

ddev import-db --file db.sql.gz
# to import the database
```

Finally you're able to execute the project executing the command `ddev start`

> For better comprehension check this recording containing the first steps:
> [![asciicast](https://asciinema.org/a/gHIIVv3X6amNdcdThfc6ISj9D.svg)](https://asciinema.org/a/gHIIVv3X6amNdcdThfc6ISj9D)


### Creating the base

I used `ddev drush…` to create the base module and form as documented in the recording below. More information can be obtained by looking at the commits made.

[![asciicast](https://asciinema.org/a/z2DJTC3R9TqgYiiHQiWzj7Mlw.svg)](https://asciinema.org/a/z2DJTC3R9TqgYiiHQiWzj7Mlw)
