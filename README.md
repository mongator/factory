# Mandango Factory [![Build Status](https://travis-ci.org/yunait/mandango-factory.png?branch=master)](http://travis-ci.org/yunait/mandango-factory)#

Mandango Factory is a PHP library to avoid the use of fixtures in your PHP unit tests. 

Using Mandango Factory instead of fixture, your unit tests will be more clear and easier to write. You’ll also be able to change the objects you create programmatically instead of being stuck with the same old fixtures. 

Mandango Factory will read the Mondator configClass definitions and make a default for every field, if you are using symfony/validator you will ge too the mandatory fields of your classes.

Mandango Factory is heavily inspired by [Phactory](http://phactory.org/).

Mandango Factory requires PHP >= 5.3.3.
