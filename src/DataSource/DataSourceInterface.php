<?php

namespace Kematjaya\ImportBundle\DataSource;

/**
 * @author Nur Hidayatullah <kematjaya0@gmail.com>
 */
interface DataSourceInterface
{
    public function startReadedRow():?int;
    
    public function keyToProcess():?string;
    
    public function execute():array;
}
