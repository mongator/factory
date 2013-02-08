<?php
namespace Mandango\Factory\Tests;
use Mandango\Tests\TestCase;
use Mandango\Factory\Factory;

class FactoryTest extends TestCase {
    public function testConstructor() {
        $factory = new Factory($this->mandango);
        $this->assertInstanceOf('Mandango\Mandango', $factory->getMandango());
    }


    public function testGenerateContainers()
    {
        $factory = new Factory($this->mandango);
        $factory->setConfigClasses(self::$staticConfigClasses);


        //var_dump($factory->getConfigClasses());
/*
        $containers = $mondator->generateContainers();

        $this->assertSame(3, count($containers));
        $this->assertTrue(isset($containers['_global']));
        $this->assertTrue(isset($containers['Article']));
        $this->assertTrue(isset($containers['Category']));
        $this->assertInstanceOf('Mandango\Mondator\Container', $containers['Article']);
        $this->assertInstanceOf('Mandango\Mondator\Container', $containers['Category']);

        $definitions = $containers['Article'];
        $this->assertSame(2, count($definitions->getDefinitions()));
        $this->assertTrue(isset($definitions['name']));
        $this->assertTrue(isset($definitions['myclass']));
        $this->assertSame('foo', $definitions['name']->getClassName());

        $definitions = $containers['Category'];
        $this->assertSame(2, count($definitions->getDefinitions()));
        $this->assertTrue(isset($definitions['name']));
        $this->assertTrue(isset($definitions['myclass']));
        $this->assertSame('bar', $definitions['name']->getClassName());*/
    }


   
}