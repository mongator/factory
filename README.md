Mongator Factory [![Build Status](https://travis-ci.org/mongator/factory.png?branch=master)](https://travis-ci.org/mongator/factory)
==============================

Mongator Factory is an alternative to using database fixtures in your PHP unit tests. Instead of maintaining a separate files of data, you define a blueprint for each table and then create as many different objects as you need in your PHP code.

By using a database factory instead of fixtures, your unit tests will be more clear and easier to write. You’ll also be able to change the objects you create programmatically instead of being stuck with the same old fixtures. 

Mongator Factory will read the Mondator configClass definitions and make a default for every field, if you are using symfony/validator you will ge too the mandatory fields of your classes.

Mongator Factory is heavily inspired by [phactory](http://phactory.org/).


Requirements
------------

* PHP 5.3.x;
* mongator/mongator
* fzaninotto/faker >= 1.1


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


Usage
--------

You can use Mongator Factory with PHPUnit, SimpleTest, or any other PHP unit test framework.

After giving Mongator Factory a mongator instance and a faker instance (faker will generate random string and numbers) you’ll define a blueprint for each model you want to create documents in. The blueprint provides default values for some or all of the columns in that collection. You can then create one or more document in that collection, and optionally override the default values for each one.

Instance of a MandangoFactory in your setUp method at TestCase and a recall in your tearDown method if you want delete all document after every test.

```php
class TestCase extends \PHPUnit_Framework_TestCase {
    protected $factory;

    protected function setUp() {
        $faker = Faker\Factory::create();
        $this->factory = new Mongator\Factory\Factory($mongator, $faker);
    }

    protected function tearDown() {
        if ($this->factory) $this->factory->recall();
    }
}
```

On your test cases just define a new fixture and after you can create all documents as you need

```php
$this->factory->define('MyFixture', 'Model\Article');
$document = $this->factory->create('Article');
```

Or maybe with some default values

```php
$this->factory->define('MyFixture', 'Model\Article', array(
    'points' => 2,
    'text' => 'faker::paragraph(2)',
    'line' => 'faker::name',
    'title' => 'text example %s',
    'updatedAt' => '1st May 2010, 01:30:00',
    'votes' => function () { return rand(200, 300); }
));
$document = $this->factory->create('Article');
```

Or the quick way
```php
$document = $this->factory->quick('Model\Article');
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