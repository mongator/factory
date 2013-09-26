<?php

/*
 * This file is part of Mongator.
 *
 * (c) Máximo Cuadros <maximo@yunait.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Mongator\Factory\Blueprint;
use Mongator\Factory\Factory;
use Mongator\Factory\Blueprint;
use Mongator\Document\Document;
use Faker\Generator;

final class DefaultGenerator
{
    public static function integer(Factory $factory, $name, array $config)
    {
        return function($sequence = null) use ($factory, $name, $config) {
            if ( $config['value'] === null ) $config['value'] = '###';
            return (int) $factory->getFaker()->numerify($config['value']);
        };
    }

    public static function float(Factory $factory, $name, array $config)
    {
        return function($sequence = null) use ($factory, $name, $config) {
            if ( is_numeric($config['value']) ) return $config['value'];
            return (float) $factory->getFaker()->numerify($config['value'])/100;
        };
    }

    public static function string(Factory $factory, $name, array $config)
    {
        if ($config['value'] === null) {
            if ( !isset($config['options']) ) $config['value'] = 'faker::sentence(6)';
            else $config['value'] = $config['options'];
        }

        return function($sequence = null) use ($factory, $name, $config) {
            if ( is_array($config['value']) ) {
                return $factory->getFaker()->randomElement($config['value']);
            }

            $string = DefaultGenerator::generate($factory->getFaker(), $config['value']);

            return sprintf($string, $sequence);
        };
    }

    public static function boolean(Factory $factory, $name, array $config)
    {
        return function($sequence = null) use ($factory, $name, $config) {
            if ( $config['value'] !== null ) return $config['value'];
            return $factory->getFaker()->randomElement(array(true, false));
        };
    }

    public static function date(Factory $factory, $name, array $config)
    {
        return function($sequence = null) use ($factory, $name, $config) {
            $value = $config['value'];
            if ( !$value ) $timestamp = time();
            else if ( is_integer($value) || is_float($value) ) $timestamp = $value;
            else {
                $generated = DefaultGenerator::generate($factory->getFaker(), $config['value']);
                if ($generated == $config['value']) {
                    $timestamp = strtotime($generated);
                } else {
                    if (!$generated instanceOf \DateTime) {
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

    public static function raw(Factory $factory, $name, array $config)
    {
        return function($sequence = null) use ($factory, $name, $config) {
            if ( $config['value'] !== null ) return $config['value'];
            return array();
        };
    }

    public static function embeddedsOne(Factory $factory, $name, array $config)
    {
        return function($sequence = null) use ($factory, $name, $config) {
            return DefaultGenerator::embedded($factory, $config['class'], $config['value']);
        };
    }

    public static function embeddedsMany(Factory $factory, $name, array $config)
    {
        return function($sequence = null) use ($factory, $name, $config) {
            $value = $config['value'];
            $documents = array();

            if (!$value) {
                $documents[] = DefaultGenerator::embedded($factory, $config['class']);
            } elseif ( is_numeric($value) ) {
                for ($i=0;$i<(int) $value;$i++) {
                    $documents[] = DefaultGenerator::embedded($factory, $config['class']);
                }
            } elseif ( is_array($value) ) {
                foreach ($value as $default) {
                    $documents[] = DefaultGenerator::embedded($factory, $config['class'], $default);
                }
            } else {
                throw new \InvalidArgumentException(
                    'Unexpected default value for embeddedsMany field'
                );
            }

            return $documents;
        };
    }

    public static function referencesOne(Factory $factory, $name, array $config)
    {
        return function($sequence = null) use ($factory, $name, $config) {
            return DefaultGenerator::reference($factory, $config['class'], $config['value']);
        };
    }

    public static function referencesMany(Factory $factory, $name, array $config)
    {
        return function($sequence = null) use ($factory, $name, $config) {
            $value = $config['value'];
            $ids = array();

            if (!$value) {
                $ids[] = DefaultGenerator::reference($factory, $config['class']);
            } elseif ( is_numeric($value) ) {
                for($i=0;$i<(int) $value;$i++) $ids[] = DefaultGenerator::reference($factory, $config['class']);
            } elseif ( is_array($value) ) {
                foreach($value as $id) $ids[] = DefaultGenerator::reference($factory, $config['class'], $id);
            } else {
                throw new \InvalidArgumentException(
                    'Unexpected default value for referencesMany field'
                );
            }

            return $ids;
        };
    }

    /* Private methods */
    public static function embedded(Factory $factory, $class, $value = null)
    {
        if ( !$value ) $value = array();
        else if ( $value instanceOf $class ) return $value->toArray()   ;

        $bp = new Blueprint($factory, $class);

        return $bp->build($value, false);
    }

    public static function reference(Factory $factory, $class, $value = null)
    {
        if ( !$value ) return $factory->quick($class);
        else if ( $value instanceOf Document ) return $value;
        else if ( is_array($value) ) return $factory->quick($class, $value);
        else if ( $value instanceOf \MongoId ) $id = $value;
        else {
            $id = new \MongoId($value);
        }

        $document = $factory->quick($class);
        $document->setId($id);

        return $document;
    }

    public static function generate(Generator $faker, $string)
    {
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
