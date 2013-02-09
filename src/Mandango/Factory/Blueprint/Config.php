<?php
namespace Mandango\Factory\Blueprint;
use Mandango\Factory\Factory;

class Config {
    const GENERATOR = 'Mandango\Factory\Blueprint\DefaultGenerator';
    private $factory;
    private $class;
    private $configClass;
    private $config = array();

    public function __construct(Factory $factory, $documentClass) {
        $this->configClass = $factory->getConfigClass($documentClass);
        $this->factory = $factory;
        $this->class = $documentClass;

        $this->setConfigBase();
        $this->setConfigValidation();
        $this->setConfigIndex();
    }

    public function getDocumentClass() 
    {
        return $this->documentClass;
    }

    public function getMandatory() 
    {
        $fields = array();
        foreach( $this->config as $field => $config ) {
            if ( $this->isMandatory($field) ) $fields[] = $field;
        }

        return $fields;
    }

    public function getDefault($field) {
        $type = $this->getType($field);

        if( method_exists(static::GENERATOR, $type) ) {
            return forward_static_call(
                static::GENERATOR . "::" . $type, 
                $this->factory, $field, $this->config[$field]
            );
        }

        return null;
    }

    public function getDefaults() {
        $defaults = array();
        foreach( $this->getMandatory() as $field ) {
            $defaults[$field] = $this->getDefault($field);
        }

        return $defaults;
    }

    public function hasField($field)
    {
        return isset($this->config[$field]);
    }

    public function isMandatory($field) 
    {
        if ( !$this->hasField($field) ) return null;
        return ( isset($this->config[$field]['mandatory']) 
            && $this->config[$field]['mandatory'] == true );
    }

    public function getType($field) 
    {
        if ( !$this->hasField($field) ) return null;
        return $this->config[$field]['type'];
    }

    public function getConfig($field) 
    {
        if ( !$this->hasField($field) ) return null;
        return $this->config[$field];
    }

    private function setConfigBase() 
    {
        foreach ($this->configClass['fields'] as $name => &$field) {
            if (is_string($field)) $field = array('type' => $field);
        }

        foreach ($this->configClass['fields'] as $name => &$field) {
            if (!is_array($field)) {
                throw new \RuntimeException(sprintf('The field "%s" of the class "%s" is not a string or array.', $name, $this->documentClass));
            }

            if (!isset($field['dbName'])) {
                $field['dbName'] = $name;
            } elseif (!is_string($field['dbName'])) {
                throw new \RuntimeException(sprintf('The dbName of the field "%s" of the class "%s" is not an string.', $name, $this->documentClass));
            }

            if (!isset($field['type'])) {
                throw new \RuntimeException(sprintf('The field "%s" of the class "%s" does not have type.', $name, $this->documentClass));
            }

            $this->config[$field['dbName']]['type'] = $field['type'];
        }

        unset($field);
        return $this->config;
    }

    private function setConfigIndex() 
    {
        foreach ($this->configClass['indexes'] as $index) {
            foreach($index['keys'] as $key) {
                
            }
        }
    }

    private function setConfigValidation() 
    {
        foreach ($this->configClass['fields'] as $name => &$field) {
            if ( isset($field['validation']) ) {
                foreach( $field['validation'] as $class => $validator ) {
                    $this->setConfigValidationForField($name, $validator);
                }
            }
        }
    }

    private function setConfigValidationForField($name,  array $validation) 
    {

       $key = key($validation);
       $config = current($validation);
        switch ($key) {
            case 'NotBlank':
                $this->config[$name]['mandatory'] = true;
                break;
            case 'Choice':
                $this->config[$name]['options'] = $config['choices'];
                break;     
        }
    }



}