<?php

namespace Kematjaya\ImportBundle\Manager;

use Kematjaya\ImportBundle\DataSource\AbstractDataSource;
use Kematjaya\ImportBundle\DataTransformer\AbstractDataTransformer;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Exception;
/**
 * @author Nur Hidayatullah <kematjaya0@gmail.com>
 */
class ImportManager implements ImportManagerInterface
{
    /**
     *
     * @var EntityManagerInterface 
     */
    private $entityManager;
    
    public function __construct(EntityManagerInterface $entityManager) 
    {
        $this->entityManager = $entityManager;
    }
    
    public function getEntityManager():EntityManagerInterface
    {
        return $this->entityManager;
    }
    
    protected function save(&$object, EntityManagerInterface $entityManager)
    {
        $entityManager->transactional(function (EntityManagerInterface $em) use ($object) {
            
            $em->persist($object);
            
        });
    }


    public function process(AbstractDataSource $source, AbstractDataTransformer $transformer): Collection 
    {
        $this->entityManager->beginTransaction();
        try{
            
            $resultSet = $source->execute();
            
            $data = $resultSet;
            if($source->keyToProcess())
            {
                if(!isset($resultSet[$source->keyToProcess()]))
                {
                    throw new Exception('cannot find key : '. $source->keyToProcess());
                }
                
                $data = $resultSet[$source->keyToProcess()];
            }
            
            $start = $source->startReadedRow() ? $source->startReadedRow() : 0;
            
            $objects = new ArrayCollection();
            foreach($data as $k => $value)
            {
                if($k < $start) continue;
                
                $entity = $transformer->fromArray($value);
                
                $this->save($entity, $this->entityManager);
                
                $objects->add($entity);
            }
            
            $this->entityManager->flush();
            
            $this->entityManager->commit();
            
            return $objects;
            
        } catch (Exception $ex) 
        {
            $this->entityManager->rollback();
            
            throw $ex;
        }
        
        return new ArrayCollection();
    }

}
