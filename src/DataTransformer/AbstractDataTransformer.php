<?php

namespace Kematjaya\ImportBundle\DataTransformer;

use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Kematjaya\ImportBundle\Exception\EmptyException;

/**
 * @author Nur Hidayatullah <kematjaya0@gmail.com>
 */
abstract class AbstractDataTransformer implements DataTransformerInterface
{
    /**
     *
     * @var EntityManagerInterface
     */
    protected $entityManager;
    
    public function __construct(EntityManagerInterface $entityManager) 
    {
        $this->entityManager = $entityManager;
    }
    
    abstract protected function getColumns():array;
    
    protected function buildException(array $data = [], string $exceptionClass = null):Exception
    {
        $message = !empty($data) ? implode(" ", $data) : "";
        
        return ($exceptionClass) ? new $exceptionClass($message) : new Exception($message);
    }
    
    protected function checkConstraints(array $data):array
    {
        if(empty($data))
        {
            throw $this->buildException([], EmptyException::class);
        }
        
        $columns = $this->getColumns();
        foreach($columns as $k => $v)
        {
            if(!isset($data[$k]))
            {
                throw $this->buildException(['undefined key:', $k]);
            }
            
            if(isset($v[self::CONSTRAINT_REQUIRED]) and $v[self::CONSTRAINT_REQUIRED] and !$data[$k])
            {
                throw $this->buildException(['column', $k, self::CONSTRAINT_REQUIRED]);
            }
            
            if(isset($v[self::CONSTRAINT_REFERENCE_CLASS]))
            {
                if(!isset($v[self::CONSTRAINT_REFERENCE_COLUMN]))
                {
                    throw $this->buildException([self::CONSTRAINT_REFERENCE_COLUMN, 'required']);
                }
                
                $referenceClass = $v[self::CONSTRAINT_REFERENCE_CLASS];
                $referenceColumn = $v[self::CONSTRAINT_REFERENCE_COLUMN];
                $class = $this->entityManager->getRepository($referenceClass)->findOneBy([$referenceColumn => $data[$k]]);
                if($class and isset($v[self::CONSTRAINT_UNIQUE]) and $v[self::CONSTRAINT_UNIQUE])
                {
                    throw $this->buildException([$k, $data[$k], 'already_exist']);
                }
                
                $data[$k] = ($class) ? $class : $data[$k];
            }
            
            $k++;
        }
        
        return $data;
    }
}
