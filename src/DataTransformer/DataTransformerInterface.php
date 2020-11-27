<?php

namespace Kematjaya\ImportBundle\DataTransformer;

/**
 * @author Nur Hidayatullah <kematjaya0@gmail.com>
 */
interface DataTransformerInterface 
{
    const CONSTRAINT_REQUIRED           = 'required';
    const CONSTRAINT_UNIQUE             = 'unique';
    const CONSTRAINT_REFERENCE_CLASS    = 'reference_class';
    const CONSTRAINT_REFERENCE_FIELD    = 'reference_field';
    
    const CONSTRAINT_TYPE_NUMBER        = 'number';
    
    const KEY_INDEX           = 'index';
    const KEY_CONSTRAINT      = 'constraint';
    const KEY_FIELD           = 'field';
    const KEY_TYPE            = 'type';
    
    public function fromArray(array $data);
}
