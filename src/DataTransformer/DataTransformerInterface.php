<?php

namespace Kematjaya\ImportBundle\DataTransformer;

/**
 * @author Nur Hidayatullah <kematjaya0@gmail.com>
 */
interface DataTransformerInterface 
{
    const CONSTRAINT_REQUIRED = 'required';
    const CONSTRAINT_UNIQUE = 'unique';
    const CONSTRAINT_REFERENCE_CLASS = 'reference_class';
    const CONSTRAINT_REFERENCE_COLUMN = 'reference_column';
    
    public function fromArray(array $data);
}
