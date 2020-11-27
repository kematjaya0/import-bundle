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
    
    protected function dataCast($value, string $type = null)
    {
        switch($type)
        {
            case self::CONSTRAINT_TYPE_NUMBER:
                return (float) $value;
                break;
            default:
                break;
        }
        
        return $value;
    }
    
    protected function checkConstraints(array $data):array
    {
        if(empty($data))
        {
            throw new EmptyException();
        }
        
        $columns = $this->getColumns();
        foreach($columns as $k => $v)
        {
            if(!isset($v[self::KEY_FIELD]))
            {
                throw new Exception(sprintf('required key: %s', self::KEY_FIELD));
            }
            if(!isset($v[self::KEY_INDEX]))
            {
                throw new Exception(sprintf('required key: %s', self::KEY_INDEX));
            }
            
            $field  = $v[self::KEY_FIELD];
            $index  = $v[self::KEY_INDEX];
            $type   = (isset($v[self::KEY_TYPE])) ? $v[self::KEY_TYPE] : null;
            $data[$index] = $this->dataCast($data[$index], $type);
            
            $constraints = (isset($v[self::KEY_CONSTRAINT])) ? $v[self::KEY_CONSTRAINT] : [];
            
            if(isset($constraints[self::CONSTRAINT_REQUIRED]) and $constraints[self::CONSTRAINT_REQUIRED] and !$data[$index])
            {
                throw new Exception(sprintf('%s %s %s', 'column', $field, self::CONSTRAINT_REQUIRED));
            }
            
            if(isset($constraints[self::CONSTRAINT_REFERENCE_CLASS]))
            {
                if(!isset($constraints[self::CONSTRAINT_REFERENCE_FIELD]))
                {
                    throw new Exception(sprintf('required "%s" constraint key', self::CONSTRAINT_REFERENCE_FIELD));
                }
                
                $referenceClass = $constraints[self::CONSTRAINT_REFERENCE_CLASS];
                $referenceField = $constraints[self::CONSTRAINT_REFERENCE_FIELD];
                
                $class = $this->entityManager->getRepository($referenceClass)->findOneBy([$referenceField => $data[$index]]);
                if($class and isset($constraints[self::CONSTRAINT_UNIQUE]) and $constraints[self::CONSTRAINT_UNIQUE])
                {
                    throw new Exception(sprintf('%s %s %s', $field, $data[$index], 'already exist'));
                }
                
                $data[$index] = ($class) ? $class : $data[$index];
            }
        }
        
        /*$columns = $this->getColumns();
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
                if(!isset($v[self::CONSTRAINT_REFERENCE_FIELD]))
                {
                    throw $this->buildException([self::CONSTRAINT_REFERENCE_COLUMN, 'required']);
                }
                
                $referenceClass = $v[self::CONSTRAINT_REFERENCE_CLASS];
                $referenceColumn = $v[self::CONSTRAINT_REFERENCE_FIELD];
                $class = $this->entityManager->getRepository($referenceClass)->findOneBy([$referenceColumn => $data[$k]]);
                if($class and isset($v[self::CONSTRAINT_UNIQUE]) and $v[self::CONSTRAINT_UNIQUE])
                {
                    throw $this->buildException([$k, $data[$k], 'already_exist']);
                }
                
                $data[$k] = ($class) ? $class : $data[$k];
            }
            
            $k++;
        }*/
        
        return $data;
    }
}
