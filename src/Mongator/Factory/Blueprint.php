<?php

/*
 * This file is part of Mongator.
 *
 * (c) Máximo Cuadros <maximo@yunait.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Mongator\Factory;
use Mongator\Factory\Blueprint\Config;
use Mongator\Factory\Blueprint\Sequence;
use Mongator\Factory\Blueprint\DefaultGenerator;

class Blueprint {
    protected $factory;
    protected $config;
    protected $class;

    protected $defaults;
    protected $sequence;
    protected $documents = array();

    public function __construct(Factory $factory, $documentClass, array $overrides = array()) 
    {
        $this->class = $documentClass;
        $this->config = new Config($factory, $documentClass);
        $this->sequence = new Sequence();

        $this->factory = $factory;
        $this->applyOverrides($overrides);
    }

    public function getDocumentClass() 
    {
        return $this->documentClass;
    }

    public function addDefault($field, $value) 
    {
        $this->defaults[$field] = $value;
    }

    public function removeDefault($field) 
    {
        unset($this->defaults[$field]);
    }

    public function applyOverrides(array $overrides)
    {
        $overrides = $this->config->fixOverrides($overrides);
        foreach ($overrides as $field => &$value ) {
            $this->config->setValue($field, $value);
            $this->config->setMandatory($field, true);
        }
    }

    public function build(array $overrides = array(), $useDBNames = true)
    {
        $position = $this->sequence->getNext();
        $data = array();
        foreach ($this->config->getDefaults($overrides, $useDBNames) as $field => $default) {
            if ( $default ) $data[$field] = $default($position);
        }

        return $data;
    }

    public function create(array $overrides = array(), $autosave = true) 
    {
        $data = $this->build($overrides);
        
        $this->documents[] = $document = $this->factory->getMongator()->create($this->class);
        $document->fromArray($data);

        if ( $autosave && !$this->config->isEmbedded() ) $document->save();
        return $document;
    }

    public function recall() 
    {
        return $this->factory->getMongator()
            ->getRepository($this->class)
            ->delete($this->documents);
    }
}