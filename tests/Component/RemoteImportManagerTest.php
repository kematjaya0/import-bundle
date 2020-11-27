<?php

namespace Kematjaya\ImportBundle\Tests\Component;

use Kematjaya\ImportBundle\Exception\EmptyException;
use Kematjaya\ImportBundle\Tests\Model\ObjectDataTrasnformerTest;
use Kematjaya\ImportBundle\DataTransformer\AbstractDataTransformer;
use Kematjaya\ImportBundle\DataSource\AbstractDataSource;
use Kematjaya\ImportBundle\DataSource\RemoteDataSource;
use Kematjaya\ImportBundle\Manager\ImportManager;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

/**
 * @author Nur Hidayatullah <kematjaya0@gmail.com>
 */
class RemoteImportManagerTest extends TestCase
{
    /**
     *
     * @var AbstractDataTransformer
     */
    private $transformer;
    
    /**
     *
     * @var EntityManagerInterface
     */
    private $entityManager;
    
    protected function setUp(): void 
    {
        parent::setUp();
        
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $this->transformer = new ObjectDataTrasnformerTest($entityManager);
        $this->entityManager = $entityManager;
    }
    
    public function testDataSource(): AbstractDataSource
    {
        $source = new RemoteDataSource('https://jsonplaceholder.typicode.com/posts');
        $this->assertNotEmpty($source->execute());
        
        return $source;
    }
    
    /**
     * @depends testDataSource
     */
    public function testProcess(AbstractDataSource $source)
    {
        $manager = new ImportManager($this->entityManager);
        $result = $manager->process($source, $this->transformer);
        $this->assertNotEmpty($result);
    }
    
    public function testEmptyException()
    {
        $this->expectException(EmptyException::class);
        $this->transformer->fromArray([]);
    }
}
