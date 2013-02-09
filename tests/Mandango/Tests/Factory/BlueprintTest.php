<?php
namespace Mandango\Factory\Tests;
use Mandango\Tests\TestCase;
use Mandango\Factory\Factory;
use Mandango\Factory\Blueprint;

class BlueprintTest extends TestCase {
    protected $configClass;

    public function testBasic()
    {
        $factory = new Factory($this->mandango, $this->faker);
        $factory->setConfigClasses(self::$staticConfigClasses);

        $blueprint = new Blueprint($factory, 'Model\Article');
       // print_r($blueprint->buildData());

        $this->assertTrue(is_array($blueprint->buildData()));
    }

    public function testDefaulstInConstructor()
    {
        $factory = new Factory($this->mandango, $this->faker);
        $factory->setConfigClasses(self::$staticConfigClasses);

        $blueprint = new Blueprint($factory, 'Model\Article', array(
            'votes' => function () { return rand(0, 100); }
        ));

        $data = $blueprint->buildData();
        $this->assertTrue(isset($data['votes']));
    }

    public function testDefaulstInBuild()
    {
        $factory = new Factory($this->mandango, $this->faker);
        $factory->setConfigClasses(self::$staticConfigClasses);

        $blueprint = new Blueprint($factory, 'Model\Article', array(
            'votes' => function () { return rand(0, 100); }
        ));

        $data = $blueprint->buildData(array(
            'votes' => function () { return rand(200, 300); }
        ));

        $this->assertTrue(isset($data['votes']));
        $this->assertTrue($data['votes'] >= 200);

        $data = $blueprint->buildData();

        $this->assertTrue(isset($data['votes']));
        $this->assertTrue($data['votes'] < 200);
    }

    public function testDefaulsStringValue()
    {
        $factory = new Factory($this->mandango, $this->faker);
        $factory->setConfigClasses(self::$staticConfigClasses);

        $blueprint = new Blueprint($factory, 'Model\Article', array(
            'points' => 2,
            'text' => 'faker::paragraph(2)',
            'line' => 'faker::name',
            'title' => 'text example %s',
            'updatedAt' => '1st May 2010, 01:30:00',
            'votes'

        ));

        $data = $blueprint->buildData();
        $this->assertEquals(2, $data['points']);
        $this->assertEquals('text example 0', $data['title']);
        $this->assertEquals(strtotime('1st May 2010, 01:30:00'), $data['updatedAt']->sec);
    }


}