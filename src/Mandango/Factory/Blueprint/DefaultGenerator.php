<?php
namespace Mandango\Factory\Blueprint;
use Mandango\Factory\Factory;
use Faker\Generator;

final class DefaultGenerator {
    static public function integer(Factory $factory, $name, array $config) {
        return function($sequence = null) use ($factory, $name, $config) {
            if ( $config['value'] === null ) $config['value'] = '###';
            return (int)$factory->getFaker()->numerify($config['value']);
        };
    }

    static public function float(Factory $factory, $name, array $config) {
        return function($sequence = null) use ($factory, $name, $config) {
            if ( is_numeric($config['value']) ) return $config['value'];
            return (float)$factory->getFaker()->numerify($config['value'])/100;
        };
    }    

    static public function string(Factory $factory, $name, array $config) {
        if ( $config['value'] === null ) $config['value'] = 'faker::sentence(6)';
        return function($sequence = null) use ($factory, $name, $config) {
            $string = static::generate($factory->getFaker(), $config['value']);
            return sprintf($string, $sequence);
        };
    }       
   
    static public function boolean(Factory $factory, $name, array $config) {
        return function($sequence = null) use ($factory, $name, $config) {
            if ( $config['value'] !== null ) return $config['value'];
            return $factory->getFaker()->randomElement(array(true, false));
        };
    }   

    static public function date(Factory $factory, $name, array $config) {
        return function($sequence = null) use ($factory, $name, $config) {
            $value = $config['value'];
            if ( !$value ) $timestamp = time();
            else if ( is_integer($value) || is_float($value) ) $timestamp = $value;
            else {
                $generated = static::generate($factory->getFaker(), $config['value']);
                if ( $generated == $config['value'] ) {
                    $timestamp = strtotime($generated);
                } else {
                    if ( !$generated instanceOf \DateTime ) {
                        throw new \InvalidArgumentException(
                            'Unexpected faker method, must return a DateTime object'
                        );
                    }
                    
                    return $generated;
                }
            }

            $date = new \DateTime();
            $date->setTimestamp($timestamp);
            return $date;
        };
    }    

    static public function referencesOne(Factory $factory, $name, array $config) {
        return function($sequence = null) use ($factory, $name, $config) {
            return static::mongoId($config['value']);
        };
    }    

    static public function referencesMany(Factory $factory, $name, array $config) {
        return function($sequence = null) use ($factory, $name, $config) {
            $value = $config['value'];
            $ids = array();

            if ( !$value ) {
                $ids[] = static::mongoId();
            } else if ( is_numeric($value) ) {
                for($i=0;$i<(int)$value;$i++) $ids[] = static::mongoId();
            } else if ( is_array($value) ) {
                foreach($value as $id) $ids[] = static::mongoId($id);
            } else {
                throw new \InvalidArgumentException(
                    'Unexpected default value for referencesMany field'
                );
            }

            return $ids;
        };
    }   

    static private function mongoId($value = null) {
        if ( !$value ) return new \MongoId();
        else if ( $value instanceOf \MongoId ) return $value;
        else {
            return new \MongoId($value);
        }
    }

    static private function generate(Generator $faker, $string) {
        preg_match('/^faker::([a-zA-Z]*)\(?([a-zA-Z0-9 ,#\?\-\:]*)\)?/', $string, $results);
        if ( count($results) == 0 ) return $string;
        else if ( count($results) == 2 ) return $faker->$results[1];
        else if ( count($results) == 3 ) {
            return call_user_func_array(
                array($faker, $results[1]), 
                explode(',', $results[2])
            );
        }
    }
}
