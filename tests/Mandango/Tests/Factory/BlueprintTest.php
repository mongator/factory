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

        $this->assertTrue(is_array($blueprint->build()));
    }

    public function testDefaulstInConstructor()
    {
        $factory = new Factory($this->mandango, $this->faker);
        $factory->setConfigClasses(self::$staticConfigClasses);

        $blueprint = new Blueprint($factory, 'Model\Article', array(
            'votes' => function () { return rand(0, 100); }
        ));

        $data = $blueprint->build();
        $this->assertTrue(isset($data['votes']));
    }

    public function testDefaulstInBuild()
    {
        $factory = new Factory($this->mandango, $this->faker);
        $factory->setConfigClasses(self::$staticConfigClasses);

        $blueprint = new Blueprint($factory, 'Model\Article', array(
            'votes' => function () { return rand(0, 100); }
        ));

        $data = $blueprint->build(array(
            'votes' => function () { return rand(200, 300); }
        ));

        $this->assertTrue(isset($data['votes']));
        $this->assertTrue($data['votes'] >= 200);

        $data = $blueprint->build();

        $this->assertTrue(isset($data['votes']));
        $this->assertTrue($data['votes'] < 200);

        $blueprint = new Blueprint($factory, 'Model\Article');

        $data = $blueprint->build(array('text'));
        $this->assertTrue(isset($data['text']));


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
            'votes',
            'author',
            'categories' => 2,
            'source'
        ));

        $data = $blueprint->build(); 
        $this->assertEquals(2, $data['points']);
        $this->assertEquals('text example 0', $data['title']);
        $this->assertEquals(
            strtotime('1st May 2010, 01:30:00'), 
            $data['updatedAt']->getTimestamp()
        );
    }

    public function testCreateAndRecall()
    {
        $factory = new Factory($this->mandango, $this->faker);
        $factory->setConfigClasses(self::$staticConfigClasses);

        $blueprint = new Blueprint($factory, 
            'Model\Article', 
            array('author', 'categories', 'source', 'comments' => 5)
        );

        $document = $blueprint->create();

        $this->assertInstanceOf('Model\Article', $document);

        $blueprint->recall();

        $result = $this->mandango
            ->getRepository('Model\Article')
            ->createQuery()
            ->criteria(array('_id' => $document->getId()))
            ->one();

        $this->assertTrue($result === null);

    }

}