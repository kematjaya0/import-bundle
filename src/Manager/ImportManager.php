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
    public function process(AbstractDataSource $source, AbstractDataTransformer $transformer, array $options = [], callable $validate = null): Collection 
    {
        $objects = new ArrayCollection();
        try{
            $start = $source->startReadedRow() ? $source->startReadedRow() : 0;
            
            $data = array_slice(
                $this->getSourceData($source, $options), 
                $start
            );
            
            if (is_callable($validate)) {
                $data = call_user_func($validate, $data, $transformer);
            }
            
            foreach ($data as $value) {
                $entity = $this->processRow($transformer, $value);
                if (null === $entity) {
                    continue;
                }
                
                $objects->add($entity);
            }
            
        } catch (Exception $ex) {
            
            throw $ex;
        }
        
        return $objects;
    }

    protected function processRow(AbstractDataTransformer $transformer, array $value = [])
    {
        if ($this->isNull($value)) {
            return null;
        }

        $entity = $transformer->fromArray($value);
        if (null === $entity) {
            return null;
        }

        $this->save($entity);
        
        return $entity;
    }
    
    /**
     * Persisted data to database
     *
     * @param type                   $object
     * @param EntityManagerInterface $entityManager
     */
    protected function save(&$object)
    {
        $entityManager = $this->getEntityManager();
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
    protected function getSourceData(AbstractDataSource $source, array $options = []):array
    {
        $resultSet = $source->execute($options);
        $data = $resultSet;
        if (!$source->keyToProcess()) {
            
            return $data;
        }
        
        if (!isset($resultSet[$source->keyToProcess()])) {
            throw new KeyNotFoundException($source->keyToProcess());
        }

        return $resultSet[$source->keyToProcess()];
    }
}
