<?php

/**
 * This file is part of Kematjaya\ImportBundle
 */

namespace Kematjaya\ImportBundle\Manager;

use Kematjaya\ImportBundle\Exception\KeyNotFoundException;
use Kematjaya\ImportBundle\DataSource\AbstractDataSource;
use Kematjaya\ImportBundle\DataTransformer\AbstractDataTransformer;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Exception;


/**
 * Implements class for handling import data
 *
 * @category Kematjaya\ImportBundle
 * @package  Kematjaya\ImportBundle\Manager
 * @license  https://opensource.org/licenses/MIT MIT
 * @author   Nur Hidayatullah <kematjaya0@gmail.com>
 */
class ImportManager implements ImportManagerInterface
{
    /**
     *
     * @var EntityManagerInterface 
     */
    private $_entityManager;
    
    
    /**
     * 
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager) 
    {
        $this->_entityManager = $entityManager;
    }
    
    /**
     * Getting entity manager object
     * 
     * @return EntityManagerInterface
     */
    public function getEntityManager():EntityManagerInterface
    {
        return $this->_entityManager;
    }
    
    /**
     * Function for process supported data to any output formatted
     *
     * @param  AbstractDataSource      $source      the source data
     * @param  AbstractDataTransformer $transformer transformer class for handle data from input format to data format
     * @return Collection 
     * @throws Exception
     */
    public function process(AbstractDataSource $source, AbstractDataTransformer $transformer): Collection 
    {
        $entityManager = $this->getEntityManager();
        $entityManager->beginTransaction();
        try{
            $data = $this->getSourceData($source);
            
            $start = $source->startReadedRow() ? $source->startReadedRow() : 0;
            $objects = new ArrayCollection();
            foreach ($data as $k => $value) {
                if ($k < $start) {
                    continue;
                }
                
                if ($this->isNull($value)) {
                    continue;
                }
                
                $entity = $transformer->fromArray($value);
                $this->save($entity, $entityManager);
                $objects->add($entity);
            }
            
            $entityManager->flush();
            $entityManager->commit();
            
            return $objects;
            
        } catch (Exception $ex) {
            $entityManager->rollback();
            
            throw $ex;
        }
        
        return new ArrayCollection();
    }

    /**
     * Persisted data to database
     *
     * @param type                   $object
     * @param EntityManagerInterface $entityManager
     */
    protected function save(&$object, EntityManagerInterface $entityManager)
    {
        $entityManager->transactional(
            function (EntityManagerInterface $em) use ($object) {
            
                $em->persist($object);
            
            }
        );
    }

    /**
     * Checking array is null or not
     * 
     * @param  array $input
     * @return bool
     */
    protected function isNull(array $input = array()):bool
    {
        return empty(
            array_filter(
                $input, function ($a) {
                    return $a !== null;
                }
            )
        );
    }
    
    /**
     * Get data from source
     * 
     * @param  AbstractDataSource $source
     * @return array set of data
     * @throws KeyNotFoundException when cannot find key to process
     */
    protected function getSourceData(AbstractDataSource $source):array
    {
        $resultSet = $source->execute();
        $data = $resultSet;
        if ($source->keyToProcess()) {
            if (!isset($resultSet[$source->keyToProcess()])) {
                throw new KeyNotFoundException($source->keyToProcess());
            }

            $data = $resultSet[$source->keyToProcess()];
        }
        
        return $data;
    }
}
