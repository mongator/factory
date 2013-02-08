<?php
namespace Mandango\Factory;

class Blueprint {
    protected $factory;
    protected $config = array();

    protected $documentClass;
    protected $configClass;

    protected $defaults;
    protected $_sequence;

    public function __construct(Factory $factory, $documentClass, array $defaults = array()) 
    {
        $this->documentClass = $documentClass;
        $this->configClass = $factory->getConfigClass($documentClass);

        $this->factory = $factory;
        $this->defaults = $defaults;

        $this->setConfigBase();
        $this->setConfigValidation();
        $this->setConfigIndex();
    }

    public function getDocumentClass() 
    {
        return $this->documentClass;
    }

    public function getConfig() 
    {
        return $this->config;
    }

    public function setDefaults(array $defaults) 
    {
        $this->defaults = $defaults;
    }

    public function addDefault($field, $value) 
    {
        $this->defaults[$field] = $value;
    }

    public function removeDefault($field) 
    {
        unset($this->defaults[$field]);
    }

 
    private function setConfigBase() {
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

            $this->config[$field['dbName']]['type'] = $name;
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



    /*
     * Build the document as an array, but don't save it to the db.
     *
     * @param array $overrides field => value pairs which override the defaults for this blueprint
     * @param array $associated [name] => [Association] pairs
     * @return array the document
     */
    public function build($overrides = array(), $associated = array()) {
        $data = $this->_defaults;
        if($associated) {
            foreach($associated as $name => $document) {
                if(!isset($this->_associations[$name])) {
                    throw new \Exception("No association '$name' defined");
                }

                $association = $this->_associations[$name];

                if(!$association instanceof Association\EmbedsMany &&
                   !$association instanceof Association\EmbedsOne) {
                    throw new \Exception("Invalid association object for '$name'");
                }

                $overrides[$name] = $document;
            }
        }

        $this->_evalSequence($data);

        if($overrides) {
            foreach($overrides as $field => $value) {
                $data[$field] = $value;
            }
        }

        return $data;
    }

    /*
     * Create document in the database and return it.
     *
     * @param array $overrides field => value pairs which override the defaults for this blueprint
     * @param array $associated [name] => [Association] pairs
     * @return array the created document
     */
    public function create($overrides = array(), $associated = array()) {
        $data = $this->build($overrides, $associated);
        $this->_collection->insert($data,array("safe"=>true));
        return $data;
    }

    /*
     * Empty the collection in the database.
     */
    public function recall() {
        $this->_collection->remove();
    }

    protected function _evalSequence(&$data) {
        $n = $this->_sequence->next();
        array_walk_recursive($data,function(&$value) use ($n) {
            if(is_string($value) && false !== strpos($value, '$')) {
                $value = eval('return "'. stripslashes($value) . '";');
            }
        });
    }
}