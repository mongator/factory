Embedded and Reference Generators
=================================

Embedded One
-------------

#### null
Create a default or empty embedded document. 

#### document
You can assign an object directly

#### array
Or you can set an array with the config, just like a normal document.


Embeddeds Many
--------------

#### null 
Create one embedded document

#### integer
If an integer is given the equal number of default documents will be created.

#### multidimensional array
An equal number of documents will be create with each inner array configuration

```php
array(
    //Embedded Document 1
    array(
        'name' => 'Sequenced text %d',
        'autho' => 'bar',
    ),

    //Embedded Document 2
    array(
        'name' => 'Sequenced text %d',
        'autho' => 'qux',
    ),
)
```

Reference One
-------------

#### null
A random MongoId will be assigned to the reference field

#### string
A string to use as the id. Must be 24 hexidecimal characters

#### mongoId
The mongoId is assigned

#### array
If the provided value is an array, a new document created with this configuration will be assigned.

```php
array(
    'name' => 'faker::lexify(????)',
    'autho' => 'bar',
);
```

Reference Many
--------------

#### null
Create one document

#### integer
If an integer is given, the equal number of default documents will be created.

#### array of strings
A equal number of mongoIds will be assigned to the reference fiel, string must be 24 hexidecimal characters

```php
array(
    '49a7011a05c677b9a916612a',
    '49a702d5450046d3d515d10d'
)
```
