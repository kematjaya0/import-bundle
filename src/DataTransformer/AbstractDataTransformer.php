<?php

namespace Kematjaya\ImportBundle\DataTransformer;

use Doctrine\ORM\EntityManagerInterface;
use Exception;
use DateTime;
use Symfony\Component\String\UnicodeString;
use Kematjaya\ImportBundle\Exception\EmptyException;

/**
 * @category Kematjaya\ImportBundle
 * @package  Kematjaya\ImportBundle\Manager
 * @license  https://opensource.org/licenses/MIT MIT
 * @author   Nur Hidayatullah <kematjaya0@gmail.com>
 */
abstract class AbstractDataTransformer implements DataTransformerInterface
{
    /**
     *
     * @var EntityManagerInterface
     */
    protected $entityManager;
    
    /**
     * 
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager) 
    {
        $this->entityManager = $entityManager;
    }
    
    protected function skippedKeys():array
    {
        return [];
    }
    
    /**
     * Parsing data from source to target formated data
     * 
     * @param  array $data
     * @return array
     * @throws EmptyException
     * @throws Exception
     */
    protected function checkConstraints(array $data):array
    {
        if (empty($data)) {
            
            throw new EmptyException();
        }
        
        $columns = $this->getColumns();
        foreach ($columns as $v) {
            if (!isset($v[self::KEY_FIELD])) {
                
                throw new Exception(sprintf('required key: %s', self::KEY_FIELD));
            }
            
            if (!isset($v[self::KEY_INDEX])) {
                
                throw new Exception(sprintf('required key: %s', self::KEY_INDEX));
            }
            
            $field  = $v[self::KEY_FIELD];
            $index  = $v[self::KEY_INDEX];
            $skippedKey = $this->skippedKeys();
            if (in_array($index, $skippedKey) and !isset($data[$index])) {
                
                continue;
            }
            
            $type   = (isset($v[self::KEY_TYPE])) ? $v[self::KEY_TYPE] : null;
            $data[$index] = isset($data[$index]) ? $this->dataCast($data[$index], $type):null;
            
            $constraints = $this->getConstraints($v);
            
            if ($this->isRequired($constraints) and (!isset($data[$index]) or !$data[$index])) {
                
                throw new Exception(sprintf('%s %s %s', 'column', $field, self::CONSTRAINT_REQUIRED));
            }
            
            if ($this->hasReferenceClass($constraints)) {
                if (!is_scalar($data[$index])) {
                    $data[$index] = null;
                    
                    continue;
                }
                
                $class = $this->getEntityValue($data[$index], $constraints);
                $data[$index] = ($class) ? $class : $data[$index];
            }
        }
        
        return $data;
    }
    
    protected function isRequired(array $constraints):bool
    {
        return isset($constraints[self::CONSTRAINT_REQUIRED]) and $constraints[self::CONSTRAINT_REQUIRED];
    }
    
    protected function getConstraints(array $data):array
    {
        return (isset($data[self::KEY_CONSTRAINT])) ? $data[self::KEY_CONSTRAINT] : [];
    }
    
    protected function hasReferenceClass(array $constraints = []):bool
    {
        return isset($constraints[self::CONSTRAINT_REFERENCE_CLASS]);
    }
    
    abstract protected function getColumns():array;
    
    /**
     * Build Exception return
     * 
     * @param  array  $data
     * @param  string $exceptionClass
     * @return Exception
     */
    protected function buildException(array $data = [], string $exceptionClass = null):Exception
    {
        $message = !empty($data) ? implode(" ", $data) : "";
        
        return ($exceptionClass) ? new $exceptionClass($message) : new Exception($message);
    }
    
    /**
     * Casting from data to specific type
     * 
     * @param  mixed  $value
     * @param  string $type
     * @return mixed
     */
    protected function dataCast($value = null, string $type = null)
    {
        if (null === $value) {
            
            return $value;
        }
        new \DateTime();
        switch ($type) {
            case self::CONSTRAINT_TYPE_NUMBER:
                return (float) $value;
            case self::CONSTRAINT_TYPE_STRING:
                return (string) $value;
            case self::CONSTRAINT_TYPE_BOOLEAN:
                return (bool) $value;
            case self::CONSTRAINT_TYPE_ARRAY:
                return is_array($value) ? $value : [$value];
            case self::CONSTRAINT_TYPE_DATE:
                $formats = ['Y-m-d H:i:s.v', 'Y-m-d H:i:s', 'Y-m-d'];
                $loop = true; $date = false;
                foreach ($formats as $format) {
                    if (!$loop) {
                        break;
                    }
                    $date = DateTime::createFromFormat($format, $value);
                    if (false !== $date) {
                        $loop = false;
                    }
                }
                
                if (false === $date) {
                    $date = new \DateTime($value);
                }
                    
                
                return $date;
            default:
                break;
        }
        
        return $value;
    }
    
    /**
     * 
     * @param string $data
     * @param array $constraints
     * @return mixed object of entity
     * @throws Exception
     */
    protected function getEntityValue(string $data, array $constraints)
    {
        if (!isset($constraints[self::CONSTRAINT_REFERENCE_FIELD])) {
            throw new Exception(sprintf('required "%s" constraint key', self::CONSTRAINT_REFERENCE_FIELD));
        }
           
        $referenceClass = $constraints[self::CONSTRAINT_REFERENCE_CLASS];
        $referenceField = $constraints[self::CONSTRAINT_REFERENCE_FIELD];
        
        $class = $this->entityManager->getRepository($referenceClass)->findOneBy([$referenceField => $data]);
        if ($class) {
            
            return $class;
        }  
        
        return $this->findInUnitOfWork($data, $referenceClass, $referenceField);
//        if ($class and isset($constraints[self::CONSTRAINT_UNIQUE]) and $constraints[self::CONSTRAINT_UNIQUE]) {
//            throw new Exception(sprintf('%s %s %s', $referenceField, $data, 'already exist'));
//        }
    }
    
    protected function findInUnitOfWork(string $data, string $referenceClass, string $referenceField)
    {
        $uow = $this->entityManager->getUnitOfWork();
        $identityMap = $uow->getIdentityMap();
        if (isset($identityMap[$referenceClass])) {
            $field = $referenceField;
            $u = new UnicodeString($field);
            $func = 'get' . $u->camel()->title();
            $obj = array_filter(
                $identityMap[$referenceClass], function ($object) use ($func, $data) {
                    return $object->$func() == $data;
                }
            );
            
            if (!empty($obj)) {
                
                return end($obj);
            }
        }
        
        $schedules = $this->entityManager->getUnitOfWork()->getScheduledEntityInsertions();
        if (empty($schedules)) {
            
            return null;
        }
        
        
        $objects = array_filter($schedules, function ($object) use ($referenceClass, $referenceField, $data) {
            $u = new UnicodeString($referenceField);
            $func = 'get' . $u->camel()->title();
            if (!$object instanceof $referenceClass) {
                return false;
            }
            
            return $object->$func() == $data;
        });
        
        if(!empty($objects)) {
            return end($objects);
        }
        
        return null;
    }
}
