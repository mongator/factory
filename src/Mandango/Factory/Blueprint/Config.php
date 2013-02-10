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

        $this->parseAndCheckFields();
        $this->parseAndCheckReferences();
        $this->parseAndCheckEmbeddeds();
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

    public function getDefault($field, $override = null) {
        $type = $this->getType($field);
        $value = $this->getValue($field);
        $config = $this->getConfig($field);
        if ( $override !== null ) $config['value'] = $override;

        if ( $value instanceOf \Closure ) return $value;
    
        if( method_exists(static::GENERATOR, $type) ) {
            return forward_static_call(
                static::GENERATOR . "::" . $type, 
                $this->factory, $field, $config
            );
        }

        return null;
    }

    public function getDefaults($overrides = array()) {
        $overrides = $this->fixOverrides($overrides);

        $defaults = array();
        foreach( $this->getMandatory() as $field ) {
            $dbName = $this->getDbName($field);
            $value = null;
            if ( isset($overrides[$field]) ) {
                $value = $overrides[$field];
                if ( $value instanceOf \Closure ) {
                    $defaults[$dbName] = $value;
                    continue;
                } 
            }

            $defaults[$dbName] = $this->getDefault($field, $value);
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

    public function getValue($field) 
    {
        if ( !$this->hasField($field) ) return null;
        return $this->config[$field]['value'];
    }

    public function getDbName($field) 
    {
        if ( !$this->hasField($field) ) return null;
        return $this->config[$field]['dbName'];
    }

    public function getConfig($field) 
    {
        if ( !$this->hasField($field) ) return null;
        return $this->config[$field];
    }

    public function setValue($field, $value) 
    {
        if ( $value === null ) return null;
        else return $this->config[$field]['value'] = $value;
    }

    public function setMandatory($field, $value) 
    {
        $this->config[$field]['mandatory'] = $value;
    }

    public function fixOverrides($overrides) 
    {
        $output = array();
        foreach($overrides as $key => $value) {
            if ( is_numeric($key) ) $output[$value] = null;
            else $output[$key] = $value;
        }

        return $output;
    }

    private function parseAndCheckFields() 
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

            $this->parseField($field, $name);
        }
    }


    private function parseAndCheckReferences() 
    {
        $merge = array();
        foreach ($this->configClass['referencesOne'] as $name => $reference) {
            $reference['type'] = 'referencesOne';
            if (!isset($reference['field'])) $reference['dbName'] = $name.'_reference_field';
            else $reference['dbName'] = $reference['field'];
            $this->parseAndCheckAssociationClass($reference, $name);

        }

        foreach ($this->configClass['referencesMany'] as $name => $reference) {
            $reference['type'] = 'referencesMany';
            if (!isset($reference['field'])) $reference['dbName'] = $name.'_reference_field';
            else $reference['dbName'] = $reference['field'];
            $this->parseAndCheckAssociationClass($reference, $name);
        }
    }

    private function parseAndCheckEmbeddeds() 
    {
        foreach ($this->configClass['embeddedsOne'] as $name => &$embedded) {
            $embedded['type'] = 'embeddedsOne';
            $this->parseAndCheckAssociationClass($embedded, $name);
        }

        foreach ($this->configClass['embeddedsMany'] as $name => &$embedded) {
            $embedded['type'] = 'embeddedsMany';
            $this->parseAndCheckAssociationClass($embedded, $name);
        }
    }

    private function parseAndCheckAssociationClass(&$field, $name)
    {
        if (!is_array($field)) {
            throw new \RuntimeException(sprintf('The association "%s" of the class "%s" is not an array or string.', $name, $this->class));
        }

        if (isset($field['class'])) {
            if (!is_string($field['class'])) {
                throw new \RuntimeException(sprintf('The class of the association "%s" of the class "%s" is not an string.', $name, $this->class));
            }

            $this->config[$name]['class'] = $field['class'];
        } elseif (isset($field['polymorphic'])) {
           //TODO: Implement polymorphic references
        } else {
            throw new \RuntimeException(sprintf('The association "%s" of the class "%s" does not have class and it is not polymorphic.', $name, $this->class));
        }

        $this->parseField($field, $name);
    }

    private function parseField(&$field, $name) 
    {
        $this->config[$name]['type'] = $field['type'];
        
        $this->config[$name]['dbName'] = $name;
        if ( isset($field['dbName']) ) {
            $this->config[$name]['dbName'] = $field['dbName'];;
        }

        $this->config[$name]['value'] = null;
        if ( isset($field['fake']) ) {
            $this->config[$name]['value'] = $field['fake'];
        }

        if ( isset($field['validation']) ) {
            foreach( $field['validation'] as $class => $validator ) {
                $this->setConfigValidationForField($name, $validator);
            }
        }     
    }


    private function setConfigValidationForField($name, array $validation) 
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