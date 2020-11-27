<?php

namespace Kematjaya\ImportBundle\Tests\Model;

use Kematjaya\ImportBundle\Tests\Model\PostTest;
use Kematjaya\ImportBundle\DataTransformer\AbstractDataTransformer;

/**
 * @author Nur Hidayatullah <kematjaya0@gmail.com>
 */
class ObjectSpreadsheetDataTrasnformerTest extends AbstractDataTransformer
{
    
    protected function getColumns(): array 
    {
        return [
            [
                self::CONSTRAINT_REQUIRED => true
            ]
        ];
    }

    public function fromArray(array $data) 
    {
        $datas = $this->checkConstraints($data);
        $entity = (new PostTest())
                ->setId($datas[0])
                ->setUserId($datas[1])
                ->setTitle($datas[2])
                ->setBody($datas[3]);
        
        return $entity;
    }

}
