Usage
=====

You can use Mongator Factory with PHPUnit, SimpleTest, or any other PHP unit test framework.

After giving Mongator Factory a mongator instance and a faker instance (faker will generate random string and numbers) you’ll define a blueprint for each model you want to create documents in. The blueprint provides default values for some or all of the columns in that collection. You can then create one or more document in that collection, and optionally override the default values for each one.


Factory Object
--------------

To use Factory you have to create a factory object.

To create the facotry object you have to pass it the mongator object fully configurated with connections, metadata, etc and a faker object, faker is a third party class used to generate random string and numbers

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

A blueprint contains the default values to be used for create the documents. You must define it for each Model you want to test. These defaults will be set on each object you create in this table, unless you override them at creation time.

Each document field type have diferent helpers, check [Default Generator] for reference.

Assuming you have a model called `article` in Mongator with `title` as mandatory string field, `author` as string field and `status` another string field, the following will define a blueprint for that model:

```php
$factory->define('DefaultArticle', 'Model\Article', array(
    'author' => 'faker::name'
));

```

The creating Documents
----------------------

Once you have defined a blueprint, you’re ready to create documents with Factory. 

You can add overrides for the defined blueprints, if you need diferent values.  

Based on our previous defined blueprint `DefaultAritcle`, here’s some examples of how you might create some article documents:

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

Usually when you are unit testing, you’ll want your database to be reset after each test. Calling `recall` Factory method you will delete all the document create with Factory keeping you database clean and tidy.


