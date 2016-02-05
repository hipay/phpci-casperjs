# phpci-casperjs

This PHPCI plugin allow you to launch casperjs tests

## Install

* First, install casperJS: http://casperjs.readthedocs.org/en/latest/installation.html

* Add hipay/phpci-casperjs in your composer.json:

```yml
[...]
    "require": {
        [...]
        "hipay/phpci-casperjs": "dev-master",
        [...]
    },
[...]
```

* Run composer install hipay/phpci-casperjs
* Copy javascript to PHPCI :

```bash
    cp /path/to/phpci/vendor/hipay/phpci-casperjs/Demorose/PHPCI/Plugin/js/casperJs.js /path/to/phpci/public/assets/js/build-plugins/casperJs.js
```

## Configuration Options

* **x_unit_file_path** - Path to xunit output. /tmp/casperOutput.xml by default.
* **tests_path** - Path to tests.
