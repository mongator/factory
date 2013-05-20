Field Generators
=================

Based on the value defined in the overrides each field will get a value generated based on it. Each field type of the Mongator collections, have diferent behaviors.


Common values for all types
---------------------------

#### null
If `null` will return a valid random value. Just set a key with value null or a key without value

```
Array(
    'title' => null, // Random string
    'author' // Random string
)
```


#### fixed value
If you configure a fixed value, the field will be setted with this value

#### clsure
You can set a clusure, this closure must be return a value suitable for this field

```
Array(
    'votes' => function() { return 4; } // int(4)
)
```

Integer
-------

#### numerify 
Any number of # char, will generate a number with equal number of digits as sharps


#### sequence
If you set the value to `%d`, return a sequence based on the uses of this blueprint.

Float
-----

#### numerify 
Any number of # char with '.' or ',', will generate a float with same format as defined.


String
------

#### sequence
If you set the value to a string with `%d`, will be pass to a sprinf function and %d will be replace with a sequence based on the uses of this blueprint.

#### faker
You can use any faker function returning a string,  setting the value to 'faker::' followed the function name and argument.
Eg.: `faker::lexify(????)` -> return a string of 4 random chars.

> You can check the available faker function at [https://github.com/fzaninotto/Faker](https://github.com/fzaninotto/Faker)

#### array
If the provided value is an array,  a random element from this array will be returned

Date
----

#### strtotime
Parse about any English textual datetime description, just like [strtotime](http://www.php.net/manual/en/function.strtotime.php)
Eg.: `21-10-2012`: int(1350770400)

#### faker
You can use any faker function returning a valide date, setting the value to 'faker::' followed the function name and argument.
Eg.: `faker::dateTimeBetween(-20 years, -10 years)` -> return a date between the given dates

