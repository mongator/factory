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
use Mongator\Factory\Blueprint;

class BlueprintTest extends TestCase {
    protected $configClass;

    public function testBasic()
    {
        $factory = new Factory($this->mongator, $this->faker);
        $blueprint = new Blueprint($factory, 'Model\Article');

        $this->assertTrue(is_array($blueprint->build()));
    }

    public function testDefaulstInConstructor()
    {
        $factory = new Factory($this->mongator, $this->faker);
        $blueprint = new Blueprint($factory, 'Model\Article', array(
            'votes' => function () { return rand(0, 100); }
        ));

        $data = $blueprint->build();
        $this->assertTrue(isset($data['votes']));
    }

    public function testDefaulstInBuild()
    {
        $factory = new Factory($this->mongator, $this->faker);
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
        $factory = new Factory($this->mongator, $this->faker);
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
        $factory = new Factory($this->mongator, $this->faker);
        $blueprint = new Blueprint($factory, 
            'Model\Article', 
            array('author', 'categories', 'source', 'comments' => 4)
        );

        $document = $blueprint->create();

        $this->assertInstanceOf('Model\Article', $document);

        $blueprint->recall();

        $result = $this->mongator
            ->getRepository('Model\Article')
            ->createQuery()
            ->criteria(array('_id' => $document->getId()))
            ->one();

        $this->assertTrue($result === null);

    }

}