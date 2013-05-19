<?php

/*
 * This file is part of Mongator.
 *
 * (c) Máximo Cuadros <maximo@yunait.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

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