<?php
namespace Mandango\Factory;
use Mandango\Mandango;
use Faker\Generator;

class Factory {
    private $mandango;
    private $faker;
    private $configClasses;
    private $blueprints;

    public function __construct(Mandango $mandango, Generator $faker)
    {
        $this->mandango = $mandango;
        $this->faker = $faker;

        $this->blueprints = array();
        $this->configClasses = array();
    }

    public function getMandango()
    {
        return $this->mandango;
    }

    public function getFaker()
    {
        return $this->faker;
    }

    public function setConfigClass($class, array $configClass)
    {
        $this->configClasses[$class] = $configClass;
    }

    public function setConfigClasses(array $configClasses)
    {
        $this->configClasses = array();
        foreach ($configClasses as $class => $configClass) {
            $this->setConfigClass($class, $configClass);
        }
    }

    public function hasConfigClass($class)
    {
        return isset($this->configClasses[$class]);
    }

    public function getConfigClass($class)
    {
        if ( !$this->hasConfigClass($class) ) return false;
        return $this->configClasses[$class];
    }

    public function hasBlueprint($blueprintName)
    {
        return isset($this->blueprints[$blueprintName]);
    }

    public function getBlueprint($blueprintName)
    {
        if ( !$this->hasBlueprint($blueprintName) ) return false;
        return $this->blueprints[$blueprintName];
    }

    public function define($blueprintName, $documentClass, array $overrides = array()) 
    {
        if ( $this->hasBlueprint($blueprintName) ) {
            throw new \InvalidArgumentException(
                sprintf('The blueprint "%s" already defined.', $blueprintName)
            );
        }
        
        $blueprint = new Blueprint($this, $documentClass, $overrides);
        return $this->blueprints[$blueprintName] = $blueprint;
    }

    public function create($blueprintName, array $overrides = array(), $autosave = true) 
    {
        if ( !$this->hasBlueprint($blueprintName) ) {
            throw new \InvalidArgumentException(
                sprintf('The blueprint "%s" does not exists.', $blueprintName)
            );
        }

        return $this->blueprints[$blueprintName]->create($overrides, $autosave);
    }

    public function recall() 
    {
        foreach ($this->blueprints as $blueprint) $blueprint->recall();   
    }
}