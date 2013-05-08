# Mongator Factory [![Build Status](https://travis-ci.org/yunait/mongator-factory.png?branch=master)](http://travis-ci.org/yunait/mongator-factory)#

Mongator Factory is a PHP library to avoid the use of fixtures in your PHP unit tests. 

Using Mongator Factory instead of fixture, your unit tests will be more clear and easier to write. You’ll also be able to change the objects you create programmatically instead of being stuck with the same old fixtures. 

Mongator Factory will read the Mondator configClass definitions and make a default for every field, if you are using symfony/validator you will ge too the mandatory fields of your classes.

Mongator Factory is heavily inspired by [Phactory](http://phactory.org/).

Mongator Factory requires PHP >= 5.3.3.
