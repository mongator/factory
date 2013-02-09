<?php
namespace Mandango\Factory\Blueprint;
use Mandango\Factory\Factory;
use Faker\Generator;

final class DefaultGenerator {
    static function integer(Factory $factory, $name, array $config) {
        return function($sequence = null) use ($factory, $name, $config) {
            return (int)$factory->getFaker()->numerify($config['value']);
        };
    }

    static function float(Factory $factory, $name, array $config) {
        return function($sequence = null) use ($factory, $name, $config) {
            return (float)$factory->getFaker()->numerify($config['value'])/10;
        };
    }    

    static function string(Factory $factory, $name, array $config) {
        if ( $config['value'] === null ) $config['value'] = 'faker::sentence(6)';
        return function($sequence = null) use ($factory, $name, $config) {
            return static::generate($factory->getFaker(), $sequence, $config['value']);
        };
    }       
   
    static function boolean(Factory $factory, $name, array $config) {
        return function($sequence = null) use ($factory, $name, $config) {
            if ( $config['value'] !== null ) return $value;
            return $factory->getFaker()->randomElement(array(true, false));
        };
    }   

    static function date(Factory $factory, $name, array $config) {
        return function($sequence = null) use ($factory, $name, $config) {
            $value = $config['value'];
            if ( !$value ) $timestamp = time();
            else if ( is_integer($value) || is_float($value) ) $timestamp = $value;
            else $timestamp = strtotime($value);

            return new \MongoDate($timestamp);
        };
    }    

    static function generate(Generator $faker, $sequence, $string) {
        preg_match('/^faker::([a-zA-Z]*)\(?(.*)\)?/', $string, $results);
        if ( count($results) == 0 ) return sprintf($string, $sequence);
        else if ( count($results) == 2 ) return $faker->$results[1];
        else if ( count($results) == 3 ) {
            return call_user_func(array($faker, $results[1]), $results[2]);
        }
    }
}
