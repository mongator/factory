<?php
namespace Mongator\Factory\Blueprint;

class Sequence {
    protected $value;

    public function __construct($start = 0) 
    {
        $this->setValue($start);
    }

    public function setValue($value)
    {
        $this->value = $value;
    }

    public function getNext()
    {
        return $this->value++;
    }

}