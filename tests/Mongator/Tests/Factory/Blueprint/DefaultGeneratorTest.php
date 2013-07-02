<?php

/*
 * This file is part of Mongator.
 *
 * (c) Máximo Cuadros <maximo@yunait.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Mongator\Factory\Tests\Blueprint;
use Mongator\Tests\TestCase;
use Mongator\Factory\Factory;
use Mongator\Factory\Blueprint\DefaultGenerator;

class DefaultGeneratorTest extends TestCase
{
    private $factory;

    public function setUp()
    {
        parent::setUp();

        $this->factory = new Factory($this->mongator, $this->faker);
    }

    public function testInteger()
    {

        $closure = DefaultGenerator::integer($this->factory, 'test', array(
            'value' => '%d'
        ));

        $this->assertTrue($closure() > 0);

        $closure = DefaultGenerator::integer($this->factory, 'test', array(
            'value' => null
        ));

        $this->assertTrue($closure() > 0);

        $closure = DefaultGenerator::integer($this->factory, 'test', array(
            'value' => 1
        ));

        $this->assertEquals(1, $closure());

        $closure = DefaultGenerator::integer($this->factory, 'test', array(
            'value' => '####'
        ));

        $this->assertTrue(is_integer($closure()));
    }

    public function testFloat()
    {
        $closure = DefaultGenerator::float($this->factory, 'test', array(
            'value' => 1.20
        ));

        $this->assertEquals(1.20, $closure());

        $closure = DefaultGenerator::float($this->factory, 'test', array(
            'value' => '####'
        ));

        $this->assertTrue(is_float($closure()));
    }

    public function testRaw()
    {
        $closure = DefaultGenerator::raw($this->factory, 'test', array(
            'value' => null
        ));

        $this->assertSame(array(), $closure());

        $array =  array('test' => 'value');
        $closure = DefaultGenerator::raw($this->factory, 'test', array(
            'value' => $array
        ));

        $this->assertSame(array('test' => 'value'), $closure());
    }

    public function testString()
    {
        $closure = DefaultGenerator::string($this->factory, 'test', array(
            'value' => null
        ));

        $this->assertTrue(is_string($closure()));

        $closure = DefaultGenerator::string($this->factory, 'test', array(
            'value' => 'Fixed text'
        ));

        $this->assertEquals('Fixed text', $closure());

        $closure = DefaultGenerator::string($this->factory, 'test', array(
            'value' => 'Sequenced text %d'
        ));

        $this->assertEquals('Sequenced text 1', $closure(1));

        $closure = DefaultGenerator::string($this->factory, 'test', array(
            'value' => 'faker::lexify(????)'
        ));

        $this->assertEquals(4,strlen($closure()));

        $closure = DefaultGenerator::string($this->factory, 'test', array(
            'value' => array('random1', 'random2')
        ));

        $this->assertTrue(is_string($closure()));

        $closure = DefaultGenerator::string($this->factory, 'test', array(
            'value' => null,
            'options' => array('random1', 'random2')
        ));

        $this->assertTrue(is_string($closure()));
    }

    public function testBoolean()
    {
        $closure = DefaultGenerator::boolean($this->factory, 'test', array(
            'value' => true
        ));

        $this->assertTrue($closure());

        $closure = DefaultGenerator::boolean($this->factory, 'test', array(
            'value' => null
        ));

        $this->assertTrue(is_bool($closure()));
    }

    public function testDate()
    {
        $now = time();
        $closure = DefaultGenerator::date($this->factory, 'test', array(
            'value' => $now
        ));

        $result = $closure();
        $this->assertEquals($now, $result->getTimestamp());

        $now = time();
        $closure = DefaultGenerator::date($this->factory, 'test', array(
            'value' => null
        ));

        $result = $closure();
        $this->assertEquals($now, $result->getTimestamp());

        $time = '21-10-2012';
        $closure = DefaultGenerator::date($this->factory, 'test', array(
            'value' => $time
        ));

        $result = $closure();
        $this->assertEquals(strtotime($time), $result->getTimestamp());

        $closure = DefaultGenerator::date($this->factory, 'test', array(
            'value' => 'faker::dateTimeBetween(-20 years, -10 years)'
        ));

        $result = $closure();
        $this->assertTrue((
            $result->getTimestamp() >= strtotime('-20 years') &&
            $result->getTimestamp() <= strtotime('-10 years')
        ));
    }

    public function testReferencesOne()
    {
        $closure = DefaultGenerator::referencesOne($this->factory, 'test', array(
            'class' => 'Model\Author',
            'value' => null
        ));

        $this->assertInstanceOf('MongoId', $closure());

        $closure = DefaultGenerator::referencesOne($this->factory, 'test', array(
            'class' => 'Model\Author',
            'value' => '49a7011a05c677b9a916612a'
        ));

        $this->assertEquals('49a7011a05c677b9a916612a', (string) $closure());

        $closure = DefaultGenerator::referencesOne($this->factory, 'test', array(
            'class' => 'Model\Author',
            'value' => new \MongoId('49a7011a05c677b9a916612a')
        ));

        $this->assertEquals('49a7011a05c677b9a916612a', (string) $closure());

        $closure = DefaultGenerator::referencesOne($this->factory, 'test', array(
            'class' => 'Model\Category',
            'value' => array( 'name' => 'my special test name'),
        ));

        $repository = $this->factory->getMongator()->getRepository('Model\Category');
        $category = $repository->findOneById($closure());

        $this->assertEquals('my special test name', $category->getName());
    }

    public function testReferencesMany()
    {
        $closure = DefaultGenerator::referencesMany($this->factory, 'test', array(
            'class' => 'Model\Category',
            'value' => null
        ));

        $this->assertInstanceOf('MongoId', current($closure()));

        $closure = DefaultGenerator::referencesMany($this->factory, 'test', array(
            'class' => 'Model\Category',
            'value' => array('49a7011a05c677b9a916612a')
        ));

        $this->assertEquals('49a7011a05c677b9a916612a', (string) current($closure()));

        $closure = DefaultGenerator::referencesMany($this->factory, 'test', array(
            'class' => 'Model\Category',
            'value' => 3
        ));

        $result = $closure();
        $this->assertEquals(3, count($result));

        foreach($result as $id) $this->assertInstanceOf('MongoId', $id);
    }

    public function testEmbeddedsOne()
    {
        $closure = DefaultGenerator::embeddedsOne($this->factory, 'test', array(
            'class' => 'Model\Source',
            'value' => null
        ));

        $this->assertTrue(is_array($closure()));

        $closure = DefaultGenerator::embeddedsOne($this->factory, 'test', array(
            'class' => 'Model\Source',
            'value' => array('name' => 'faker::name', 'text')
        ));

        $data = $closure();
        $this->assertTrue(strlen($data['name']) > 0);
        $this->assertTrue(strlen($data['text']) > 0);
        $this->assertFalse(isset($data['note']));

        $closure = DefaultGenerator::embeddedsOne($this->factory, 'test', array(
            'class' => 'Model\Source',
            'value' => $this->factory->getMongator()->create('Model\Source')
        ));

        $this->assertTrue(is_array($closure()));
    }

    public function testEmbeddedsMany()
    {
        $closure = DefaultGenerator::embeddedsMany($this->factory, 'test', array(
            'class' => 'Model\Source',
            'value' => null
        ));

        $this->assertTrue(is_array(current($closure())));

        $closure = DefaultGenerator::embeddedsMany($this->factory, 'test', array(
            'class' => 'Model\Source',
            'value' => array(
                array('text'),
                array('name')
            )
        ));

        $result = $closure();
        $this->assertEquals(2, count($result));
        $this->assertTrue(strlen($result[0]['text']) > 0);
        $this->assertTrue(strlen($result[1]['name']) > 0);

        $closure = DefaultGenerator::embeddedsMany($this->factory, 'test', array(
            'class' => 'Model\Source',
            'value' => 3
        ));

        $result = $closure();
        $this->assertEquals(3, count($result));
        foreach($result as $id) $this->assertTrue(is_array($id));
    }
}
