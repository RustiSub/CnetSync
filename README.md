CnetSync
========

[![Build Status](https://secure.travis-ci.org/NoUseFreak/CnetSync.png)](https://travis-ci.org/NoUseFreak/CnetSync)

CnetSync is a library that should help you sync events from the cultuurnet database.

## Installation

As a temporary fix, you will need to tell composer what version of cdb it should use. This will be fixed once cultuurnet tags the version.

composer.json

```
{
    "require": {
        "nousefreak/cnetsync": "1.0.*",
        "cultuurnet/cdb": "dev-master"
    }
}
```

## Usage
```php
<?php

$config = new \CnetSync\Configuration\Configuration();
$config->setApiKey('xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx');

$sync = new \CnetSync\CnetSync($config);
$sync->setPersister(new \CnetSync\Persister\Persister());

$sync->run();
```

## Contributing

> All code contributions - including those of people having commit access - must
> go through a pull request and approved by a core developer before being
> merged. This is to ensure proper review of all the code.
>
> Fork the project, create a feature branch, and send us a pull request.
>
> To ensure a consistent code base, you should make sure the code follows
> the [Coding Standards](http://symfony.com/doc/2.0/contributing/code/standards.html)
> which we borrowed from Symfony.
> Make sure to check out [php-cs-fixer](https://github.com/fabpot/PHP-CS-Fixer) as this will help you a lot.

If you would like to help take a look at the [list of issues](http://github.com/NoUseFreak/CnetSync/issues).

## Requirements

PHP 5.3.2 or above

## Author and contributors

Dries De Peuter - <dries@nousefreak.be> - <http://nousefreak.be>

See also the list of [contributors](https://github.com/NoUseFreak/CnetSync/contributors) who participated in this project.

## License

Notifier is licensed under the MIT license.
