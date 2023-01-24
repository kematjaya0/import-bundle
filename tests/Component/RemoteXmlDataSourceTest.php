<?php

/**
 * This file is part of the import-bundle.
 */

namespace Kematjaya\ImportBundle\Tests\Component;

use Kematjaya\ImportBundle\DataSource\RemoteXmlDataSource;
use PHPUnit\Framework\TestCase;

/**
 * @package Kematjaya\ImportBundle\Tests\Component
 * @license https://opensource.org/licenses/MIT MIT
 * @author  Nur Hidayatullah <kematjaya0@gmail.com>
 */
class RemoteXmlDataSourceTest extends TestCase 
{
    public function testDataSourceException(): void
    {
        $source = new RemoteXmlDataSource('https://jsonplaceholder.typicode.com/posts');
        $this->expectException(\Exception::class);
        $source->execute();
    }
    
    public function testDataSourceValid(): void
    {
        $source = new RemoteXmlDataSource('http://103.142.21.15/paket.xml');
        
        $this->assertIsArray($source->execute());
    }
}
