<?php
namespace Mandango\Factory;
use Mandango\Mandango;
use Faker\Generator;

class Factory {
    private $mandango;
    private $faker;
    private $configClasses;

    public function __construct(Mandango $mandango, Generator $faker)
    {
        $this->mandango = $mandango;
        $this->faker = $faker;

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

    public function getConfigClasses()
    {
        return $this->configClasses;
    }

    public function getConfigClass($class)
    {
        if (!$this->hasConfigClass($class)) {
            throw new \InvalidArgumentException(sprintf('The config class "%s" does not exists.', $class));
        }

        return $this->configClasses[$class];
    }



    public function define($blueprintName, $definition, $defaultsOverride = array(), $associations = array()) 
    {
        $defaults = $this->getDefault($definition);
        return parent::define($blueprintName, $defaults, $associations);
    }


}