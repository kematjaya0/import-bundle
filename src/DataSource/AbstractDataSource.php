<?php

/**
 * This file is part of Kematjaya\ImportBundle
 */

namespace Kematjaya\ImportBundle\DataSource;

/**
 * @category Kematjaya\ImportBundle
 * @package  Kematjaya\ImportBundle\Manager
 * @license  https://opensource.org/licenses/MIT MIT
 * @author   Nur Hidayatullah <kematjaya0@gmail.com>
 */
abstract class AbstractDataSource implements DataSourceInterface
{
    /**
     *
     * @var string
     */
    private $data_key;

    /**
     *
     * @var int
     */
    private $row;
    
    /**
     * Get key to process data
     * 
     * @return string|null
     */
    public function keyToProcess(): ?string 
    {
        return $this->data_key;
    }

    /**
     * Set key to process data
     * 
     * @param  string $key
     * @return self
     */
    public function setKeyToProcess(string $key):self
    {
        $this->data_key = $key;
        
        return $this;
    }
    
    /**
     * Get start readed row
     * 
     * @return int|null
     */
    public function startReadedRow(): ?int 
    {
        return $this->row;
    }

    /**
     * Set start readed row
     * 
     * @param  int $row
     * @return self
     */
    public function setReadedRow(int $row):self
    {
        $this->row = $row;
        
        return $this;
    }
}
