Usage
=====

You can use Mongator Factory with PHPUnit, SimpleTest or any other PHP unit test framework.

After giving Mongator Factory a mongator and a faker instance (the faker will generate random strings and numbers) you’ll need to define a blueprint for each model you want to create documents in. The blueprint provides default values for some or all of the columns in that collection. You can then create one or more documents in that collection and optionally override the default values for each one.


Factory Object
--------------

To use the Factory you have to create a factory object.

To create it, you have to pass the fully configurated mongator object (with connections, metadata, etc.) and a faker object. The faker is a third party class used to generate random strings and numbers.

```php

$connection = new Connection('mongodb://localhost:27017', 'test');
$mongator = new Mongator(new Model\Mapping\MetadataFactory);
$mongator->setConnection('default', $connection);
$mongator->setDefaultConnectionName('default');

$faker = Faker\Factory::create()

$factory = new Mongator\Factory\Factory($mongator, $faker);
```


Defining Blueprints
-------------------

A blueprint contains default values for your documents. You must define one for each Model you want to test. These defaults will be set on each object you create in this table, unless you override them at creation time.

Each document field type has different helpers. You can check the [Default Generator] for reference.

Assuming you have a model called `article` in Mongator with `title` as mandatory string field, `author` as string field and `status` as another string field, the following would define a blueprint for that model:

```php
$factory->define('DefaultArticle', 'Model\Article', array(
    'author' => 'faker::name'
));

```

The creating Documents
----------------------

Once you have defined a blueprint, you’re ready to create documents with Factory. 

You can add overrides for the defined blueprints, if you need different values.  

Based on our previous defined blueprint `DefaultAritcle`, here are some examples of how you could create some article documents:

### Example #1: Without overrides

```php
$article = $factory->create('DefaultArticle');
print_r($article->toArray()):
```

Result:
```php
Array
(
    [title] => Sint velit eveniet. Rerum atque repellat voluptatem
    [author] => Adaline Reichel
)
```

### Example #2: With overrides, undefined fields

```php
$article = $factory->create('DefaultArticle', array('status' => 'published'));
print_r($article->toArray()):
```

Result:
```php
Array
(
    [title] => Sint velit eveniet. Rerum atque repellat voluptatem
    [author] => Gracie Weber
    [status] => published
)
```

### Example #3: With overrides, previous defined fields

```php
$article = $factory->create('DefaultArticle', array('author' => 'Dr. Zoidberg'));
print_r($article->toArray()):
```

Result:
```php
Array
(
    [title] => Sint velit eveniet. Rerum atque repellat voluptatem
    [author] => Dr. Zoidberg
)
```

Recall
------

Usually, during unit testing, you’ll want your database to be reset after each test. Calling the `recall` Factory method you'll delete all the document created with Factory, thus keeping your database clean and tidy.


