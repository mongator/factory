Mongator Factory [![Build Status](https://travis-ci.org/mongator/factory.png?branch=master)](https://travis-ci.org/mongator/factory)
==============================

Mongator Factory is a PHP library to avoid the use of fixtures in your PHP unit tests. 

Using Mongator Factory instead of fixture, your unit tests will be more clear and easier to write. You’ll also be able to change the objects you create programmatically instead of being stuck with the same old fixtures. 

Mongator Factory will read the Mondator configClass definitions and make a default for every field, if you are using symfony/validator you will ge too the mandatory fields of your classes.

Mongator Factory is heavily inspired by [Phactory](http://phactory.org/).

Requirements
------------

* PHP 5.3.x;
* mongator/mongator


Installation
------------

The recommended way to install Mongator Factory is [through composer](http://getcomposer.org).
You can see [package information on Packagist.](https://packagist.org/packages/mongator/factory)

```JSON
{
    "require": {
        "mongator/factory": "dev"
    }
}
```


Examples
--------
On your test cases just define a new fixture and after you can create all documents as you need

```php
$factory->define('MyFixture', 'Model\Article');
$document = $factory->create('Article');
```

Or maybe with some default values

```php
$factory->define('MyFixture', 'Model\Article', array(
    'points' => 2,
    'text' => 'faker::paragraph(2)',
    'line' => 'faker::name',
    'title' => 'text example %s',
    'updatedAt' => '1st May 2010, 01:30:00',
    'votes' => function () { return rand(200, 300); }
));
$document = $factory->create('Article');
```

Or the quick way
```php
$document = $factory->quick('Model\Article');
```

Tests
-----

Tests are in the `tests` folder.
To run them, you need PHPUnit.
Example:

    $ phpunit --configuration phpunit.xml.dist


License
-------

MIT, see [LICENSE](LICENSE)