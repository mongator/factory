<?php
namespace Mandango\Factory;
use Mandango\Factory\Blueprint\Config;
use Mandango\Factory\Blueprint\Sequence;
use Mandango\Factory\Blueprint\DefaultGenerator;

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

    public function build(array $overrides = array())
    {
        $position = $this->sequence->getNext();
        foreach ($this->config->getDefaults($overrides) as $field => $default) {
            if ( $default ) $data[$field] = $default($position);
        }

        return $data;
    }

    public function create($overrides = array(), $autosave = true) 
    {
        $data = $this->build($overrides);
        
        $this->documents[] = $document = $this->factory->getMandango()->create($this->class);
        $document->fromArray($data);

        if ( $autosave ) $document->save();
        return $document;
    }

    public function recall() 
    {
        return $this->factory->getMandango()
            ->getRepository($this->class)
            ->delete($this->documents);
    }
}