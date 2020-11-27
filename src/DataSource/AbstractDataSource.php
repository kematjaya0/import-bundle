<?php

namespace Kematjaya\ImportBundle\DataSource;

/**
 * @author Nur Hidayatullah <kematjaya0@gmail.com>
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
    
    public function keyToProcess(): ?string 
    {
        return $this->data_key;
    }

    public function setKeyToProcess(string $key):self
    {
        $this->data_key = $key;
        
        return $this;
    }
    
    public function startReadedRow(): ?int 
    {
        return $this->row;
    }

    public function setReadedRow(int $row):self
    {
        $this->row = $row;
        
        return $this;
    }
}
