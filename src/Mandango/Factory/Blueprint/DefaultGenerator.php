<?php
namespace Mandango\Factory\Blueprint;
use Mandango\Factory\Factory;

final class DefaultGenerator {
    static function integer(Factory $factory, $name, array $config, $value = false) {
        return function($sequence = null) use ($factory, $name, $config, $value) {
            return $sequence;
        };
    }

    static function string(Factory $factory, $name, array $config, $value = false) {
        return function($sequence = null) use ($factory, $name, $config, $value) {
            return sprintf('Test "%s" #%d', $name, $sequence);
        };
    }       

    static function float(Factory $factory, $name, array $config, $value = false) {

    }       

    static function boolean(Factory $factory, $name, array $config, $value = false) {

    }   

    static function date(Factory $factory, $name, array $config, $value = false) {
        return function($sequence = null) use ($factory, $name, $config, $value) {
            if ( !$value ) $timestamp = time();
            else if ( is_integer($value) || is_float($value) ) $timestamp = $value;
            else $timestamp = strtotime($value);

            return new \MongoDate($timestamp);
        };
    }    

    static function fixed(Factory $factory, $name, array $config, $value = false) {
        return function($sequence = null) use ($factory, $name, $config, $value) {
            if ( is_string($value) ) return sprintf($value, $sequence);
            return $value;
        };
    }

    static function faker(Factory $factory, $name, array $config, $value = false) {
        return function($sequence = null) use ($factory, $name, $config, $value) {
            return $factory->getFaker()->$value;
        };
    }              
}
