Field Generators
=================

Each field will get a generated value based on those defined in the overrides. Each field type of the Mongator collections has different behaviors.

Common values for all types
---------------------------

#### null
If set to `null`, it will return a valid random value. Just set a key with null value or a void key.

```
Array(
    'title' => null, // Random string
    'author' // Random string
)
```


#### fixed value
You can configure a fixed value with which the field will be set.

#### closure
You can set a clusure, which must return a value suitable for this field.

```
Array(
    'votes' => function() { return 4; } // int(4)
)
```

Integer
-------

#### numerify 
For generating random integers of specific length, set as many '#' characters as you want your integer length to be.


#### sequence
If you set the value to `%d`, it will return an integer for each generated instance. Each value will be increased by one in regard to the previously generated instance.

Float
-----

#### numerify 
Same as for integers, except for the inclusion of '.' or ',' characters for separationg decimals from units.


String
------

#### sequence
If you set the value to a string with `%d`, it will be pass the string to a sprinf function and %d will be replaced with a sequence based on the uses of this blueprint.

#### faker
You can use any faker function returning a string,  setting the value to 'faker::' followed the function name and argument.
Eg.: `faker::lexify(????)` -> return a string of 4 random chars.

> You can check the available faker function at [https://github.com/fzaninotto/Faker](https://github.com/fzaninotto/Faker)

#### array
If the provided value is an array, a random element from this array will be returned

Date
----

#### strtotime
Parse any English textual datetime description, just like [strtotime](http://www.php.net/manual/en/function.strtotime.php)
Eg.: `21-10-2012`: int(1350770400)

#### faker
You can use any faker function returning a valide date, setting the value to 'faker::' followed the function name and argument.
Eg.: `faker::dateTimeBetween(-20 years, -10 years)` -> return a date between the given dates

