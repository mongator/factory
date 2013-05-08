<?php
namespace Mandango\Tests;

use Mandango\Cache\ArrayCache;
use Mandango\Connection;
use Mandango\Mandango;
use Mandango\Archive;
use Mandango\Type\Container as TypeContainer;
use Faker\Factory;

class TestCase extends \PHPUnit_Framework_TestCase
{   
    static protected $staticFaker;
    static protected $staticConnection;
    static protected $staticMandango;
    static protected $staticConfigClasses;

    protected $metadataFactoryClass = 'Model\Mapping\MetadataFactory';
    protected $server = 'mongodb://localhost:27017';
    protected $dbName = 'mandango_factory_tests';

    protected $connection;
    protected $mandango;
    protected $unitOfWork;
    protected $metadataFactory;
    protected $cache;
    protected $mongo;
    protected $db;

    protected function setUp()
    {
        if (!static::$staticConnection) {
            static::$staticConnection = new Connection($this->server, $this->dbName);
        }
        $this->connection = static::$staticConnection;

        if (!static::$staticFaker) {
            static::$staticFaker = Factory::create();
        }


        if (!static::$staticMandango) {
            static::$staticMandango = new Mandango(new $this->metadataFactoryClass);
            static::$staticMandango->setConnection('default', $this->connection);
            static::$staticMandango->setDefaultConnectionName('default');
        }

        if (!static::$staticConfigClasses) {
            static::$staticConfigClasses = require __DIR__.'/../../configClasses.php';
        }

        $this->faker = static::$staticFaker;
        $this->mandango = static::$staticMandango;
        $this->unitOfWork = $this->mandango->getUnitOfWork();
        $this->metadataFactory = $this->mandango->getMetadataFactory();
        $this->cache = $this->mandango->getFieldsCache();

        foreach ($this->mandango->getAllRepositories() as $repository) {
            $repository->getIdentityMap()->clear();
        }

        $this->mongo = $this->connection->getMongo();
        $this->db = $this->connection->getMongoDB();

        foreach ($this->db->listCollections() as $collection) {
          //  $collection->drop();
        }
    }

    protected function tearDown()
    {
        Archive::clear();
        TypeContainer::reset();
    }
}
