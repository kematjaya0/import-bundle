<?php

namespace Kematjaya\ImportBundle\Tests\Model;

use Kematjaya\ImportBundle\Tests\Model\PostTest;
use Kematjaya\ImportBundle\DataTransformer\AbstractDataTransformer;

/**
 * @author Nur Hidayatullah <kematjaya0@gmail.com>
 */
class ObjectDataTrasnformerTest extends AbstractDataTransformer
{
    
    public function fromArray(array $data) 
    {
        $datas = $this->checkConstraints($data);
        $entity = (new PostTest())
                ->setId($datas['id'])
                ->setUserId($datas['userId'])
                ->setTitle($datas['title'])
                ->setBody($datas['body']);
        
        return $entity;
    }

    protected function getColumns(): array 
    {
        return [
            [
                self::KEY_FIELD => 'id',
                self::KEY_INDEX => 'id',
                self::KEY_CONSTRAINT => [
                    self::CONSTRAINT_REQUIRED => true
                ]
            ]
        ];
    }

}
