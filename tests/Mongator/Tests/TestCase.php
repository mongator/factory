<?php
namespace Mongator\Tests;

use Mongator\Cache\ArrayCache;
use Mongator\Connection;
use Mongator\Mongator;
use Mongator\Archive;
use Mongator\Type\Container as TypeContainer;
use Faker\Factory;

class TestCase extends \PHPUnit_Framework_TestCase
{   
    static protected $staticFaker;
    static protected $staticConnection;
    static protected $staticMongator;
    static protected $staticConfigClasses;

    protected $metadataFactoryClass = 'Model\Mapping\MetadataFactory';
    protected $server = 'mongodb://localhost:27017';
    protected $dbName = 'mongator_factory_tests';

    protected $connection;
    protected $mongator;
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


        if (!static::$staticMongator) {
            static::$staticMongator = new Mongator(new $this->metadataFactoryClass);
            static::$staticMongator->setConnection('default', $this->connection);
            static::$staticMongator->setDefaultConnectionName('default');
        }

        if (!static::$staticConfigClasses) {
            static::$staticConfigClasses = require __DIR__.'/../../configClasses.php';
        }

        $this->faker = static::$staticFaker;
        $this->mongator = static::$staticMongator;
        $this->unitOfWork = $this->mongator->getUnitOfWork();
        $this->metadataFactory = $this->mongator->getMetadataFactory();
        $this->cache = $this->mongator->getFieldsCache();

        foreach ($this->mongator->getAllRepositories() as $repository) {
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
