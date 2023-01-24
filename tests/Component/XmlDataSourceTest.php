<?php

/**
 * This file is part of the import-bundle.
 */

namespace Kematjaya\ImportBundle\Tests\Component;

use Kematjaya\ImportBundle\DataSource\XmlDataSource;
use PHPUnit\Framework\TestCase;

/**
 * @package Kematjaya\ImportBundle\Tests\Component
 * @license https://opensource.org/licenses/MIT MIT
 * @author  Nur Hidayatullah <kematjaya0@gmail.com>
 */
class XmlDataSourceTest extends TestCase 
{
    public function testDataSourceValid(): void
    {
        $source = new XmlDataSource(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Model/paket.xml');
        
        $this->assertIsArray($source->execute());
    }
}
