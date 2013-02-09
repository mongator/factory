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
        foreach ($overrides as $field => &$value ) {
            $this->config->setValue($field, $value);
            $this->config->setMandatory($field, true);
        }
    }

    public function buildData(array $overrides = array())
    {
        $position = $this->sequence->getNext();
        foreach ($this->config->getDefaults($overrides) as $field => $default) {
            if ( $default ) $data[$field] = $default($position);
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