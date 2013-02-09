<?php
namespace Mandango\Factory\Tests\Blueprint;
use Mandango\Tests\TestCase;
use Mandango\Factory\Factory;
use Mandango\Factory\Blueprint\DefaultGenerator;

class DefaultGeneratorTest extends TestCase {
    private $factory;

    public function setUp() {
        parent::setUp();

        $this->factory = new Factory($this->mandango);
        $this->factory->setConfigClasses(self::$staticConfigClasses);
    }   

    public function testInteger()
    {
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

    public function testString()
    {
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
        $this->assertEquals($now, $result->sec);

        $now = time();
        $closure = DefaultGenerator::date($this->factory, 'test', array(
            'value' => null
        ));

        $result = $closure();
        $this->assertEquals($now, $result->sec);

        $time = '21-10-2012';
        $closure = DefaultGenerator::date($this->factory, 'test', array(
            'value' => $time
        ));

        $result = $closure();
        $this->assertEquals(strtotime($time), $result->sec);
       
        $closure = DefaultGenerator::date($this->factory, 'test', array(
            'value' => 'faker::dateTimeBetween(-20 years, -10 years)'
        ));

        $result = $closure();
        $this->assertTrue(( 
            $result->sec >= strtotime('-20 years') && 
            $result->sec <= strtotime('-10 years')
        ));        
    }  
}