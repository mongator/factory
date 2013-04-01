<?php
namespace Mandango\Factory\Tests;
use Mandango\Tests\TestCase;
use Mandango\Factory\Factory;

class FactoryTest extends TestCase {
    public function testConstructor() 
    {
        $factory = new Factory($this->mandango, $this->faker);
        $this->assertInstanceOf('Mandango\Mandango', $factory->getMandango());
    }

    public function testConfigs()
    {
        $factory = new Factory($this->mandango, $this->faker);
        $this->assertTrue(is_array($factory->getConfigClass('Model\Article')));
    }

    public function testBlueprint()
    {
        $factory = new Factory($this->mandango, $this->faker);

        $this->assertTrue($factory->hasConfigClass('Model\Article'));
        $this->assertFalse($factory->hasBlueprint('Article'));

        $blueprint = $factory->define('Article', 'Model\Article');
        $this->assertInstanceOf('Mandango\Factory\Blueprint', $blueprint);

        $this->assertTrue($factory->hasBlueprint('Article'));

        $document = $factory->create('Article', array(), false);
        $this->assertInstanceOf('Model\Article', $document);
    }


    public function testQuick()
    {
        $factory = new Factory($this->mandango, $this->faker);

        $document = $factory->quick('Model\Article', array(), false);
        $this->assertInstanceOf('Model\Article', $document);

        $document = $factory->quick('Model\Article', array(), false);
        $this->assertInstanceOf('Model\Article', $document);

        $this->assertTrue($factory->hasBlueprint('Model\Article'));
    }


    public function testRecall()
    {
        $factory = new Factory($this->mandango, $this->faker);
       
        $factory->define('Article', 'Model\Article');
        $document = $factory->create('Article');

        $factory->recall();
    }
    

    /**
     * @expectedException InvalidArgumentException
     */
    public function testDefineException()
    {
        $factory = new Factory($this->mandango, $this->faker);

        $blueprint = $factory->define('Article', 'Model\Article');
        $blueprint = $factory->define('Article', 'Model\Article');
    }
}