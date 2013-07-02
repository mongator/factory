<?php

/*
 * This file is part of Mongator.
 *
 * (c) Máximo Cuadros <maximo@yunait.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Mongator\Factory\Tests;
use Mongator\Tests\TestCase;
use Mongator\Factory\Factory;

class FactoryTest extends TestCase
{
    public function testConstructor()
    {
        $factory = new Factory($this->mongator, $this->faker);
        $this->assertInstanceOf('Mongator\Mongator', $factory->getMongator());
    }

    public function testConfigs()
    {
        $factory = new Factory($this->mongator, $this->faker);
        $this->assertTrue(is_array($factory->getConfigClass('Model\Article')));
    }

    public function testBlueprint()
    {
        $factory = new Factory($this->mongator, $this->faker);

        $this->assertTrue($factory->hasConfigClass('Model\Article'));
        $this->assertFalse($factory->hasBlueprint('Article'));

        $blueprint = $factory->define('Article', 'Model\Article');
        $this->assertInstanceOf('Mongator\Factory\Blueprint', $blueprint);

        $this->assertTrue($factory->hasBlueprint('Article'));

        $document = $factory->create('Article', array(), false);
        $this->assertInstanceOf('Model\Article', $document);
    }

    public function testQuick()
    {
        $factory = new Factory($this->mongator, $this->faker);

        $document = $factory->quick('Model\Article', array(), false);
        $this->assertInstanceOf('Model\Article', $document);

        $document = $factory->quick('Model\Article', array(), false);
        $this->assertInstanceOf('Model\Article', $document);

        $this->assertTrue($factory->hasBlueprint('Model\Article'));
    }

    public function testRecall()
    {
        $factory = new Factory($this->mongator, $this->faker);

        $factory->define('Article', 'Model\Article');
        $document = $factory->create('Article');

        $factory->recall();
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testDefineException()
    {
        $factory = new Factory($this->mongator, $this->faker);

        $blueprint = $factory->define('Article', 'Model\Article');
        $blueprint = $factory->define('Article', 'Model\Article');
    }
}
