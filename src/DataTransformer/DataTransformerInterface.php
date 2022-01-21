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
    const CONSTRAINT_TYPE_STRING        = 'string';
    const CONSTRAINT_TYPE_ARRAY         = 'array';
    const CONSTRAINT_TYPE_BOOLEAN       = 'boolean';
    const CONSTRAINT_TYPE_DATE          = 'date';
    
    const KEY_INDEX           = 'index';
    const KEY_CONSTRAINT      = 'constraint';
    const KEY_FIELD           = 'field';
    const KEY_TYPE            = 'type';
    
    
    const TAG_NAME = 'kematjaya.data_transformer';
    
    /**
     * Process from array to object
     * 
     * @param array $data
     */
    public function fromArray(array $data);
}
