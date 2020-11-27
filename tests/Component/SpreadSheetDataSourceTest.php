<?php

namespace Kematjaya\ImportBundle\Tests\Component;

use Kematjaya\ImportBundle\DataSource\AbstractDataSource;
use Kematjaya\ImportBundle\DataSource\SpreadSheetDataSource;
use Kematjaya\ImportBundle\DataTransformer\AbstractDataTransformer;
use Kematjaya\ImportBundle\Tests\Model\ObjectSpreadsheetDataTrasnformerTest;
use Kematjaya\ImportBundle\Manager\ImportManager;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

/**
 * @author Nur Hidayatullah <kematjaya0@gmail.com>
 */
class SpreadSheetDataSourceTest extends TestCase
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
        $this->transformer = new ObjectSpreadsheetDataTrasnformerTest($entityManager);
        $this->entityManager = $entityManager;
    }
    
    public function testDataSource(): AbstractDataSource
    {
        $source = (new SpreadSheetDataSource(__DIR__ . '/../Model/post.xlsx'))
                ->setReadedRow(1);
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
}
