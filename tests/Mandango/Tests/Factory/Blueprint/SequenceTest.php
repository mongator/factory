<?php
namespace Mandango\Factory\Tests\Blueprint;
use Mandango\Tests\TestCase;
use Mandango\Factory\Blueprint\Sequence;

class SequenceTest extends TestCase {
    protected $configClass;
    protected $instance;

    public function testWithNoStart()
    {
        $sequence = new Sequence();
        $this->assertSame(0, $sequence->getNext());
        $this->assertSame(1, $sequence->getNext());
    }  

    public function testWithStart()
    {
        $sequence = new Sequence(10);
        $this->assertSame(10, $sequence->getNext());
        $this->assertSame(11, $sequence->getNext());

    }  

}