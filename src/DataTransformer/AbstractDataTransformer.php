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
            $type   = (isset($v[self::KEY_TYPE])) ? $v[self::KEY_TYPE] : null;
            $data[$index] = $this->dataCast($data[$index], $type);
            
            $constraints = (isset($v[self::KEY_CONSTRAINT])) ? $v[self::KEY_CONSTRAINT] : [];
            
            if (isset($constraints[self::CONSTRAINT_REQUIRED]) and $constraints[self::CONSTRAINT_REQUIRED] and !$data[$index]) {
                throw new Exception(sprintf('%s %s %s', 'column', $field, self::CONSTRAINT_REQUIRED));
            }
            
            if (isset($constraints[self::CONSTRAINT_REFERENCE_CLASS])) {
                
                $class = $this->getEntityValue($data[$index], $constraints);
                $data[$index] = ($class) ? $class : $data[$index];
            }
        }
        
        return $data;
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
    protected function dataCast($value, string $type = null)
    {
        switch($type)
        {
            case self::CONSTRAINT_TYPE_NUMBER:
                return (float) $value;
                    break;
            case self::CONSTRAINT_TYPE_STRING:
                return (string) $value;
            case self::CONSTRAINT_TYPE_BOOLEAN:
                return (bool) $value;
            case self::CONSTRAINT_TYPE_ARRAY:
                return is_array($value) ? $value : [$value];
            case self::CONSTRAINT_TYPE_DATE:
                $date = DateTime::createFromFormat('Y-m-d', $value);
                if (false === $date) {
                    throw new Exception(sprintf('invalid date format "%s", available: %s', $value, 'Y-m-d'));
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

            if(!empty($obj)) {
                $class = end($obj);
            }
        }
        
        if ($class and isset($constraints[self::CONSTRAINT_UNIQUE]) and $constraints[self::CONSTRAINT_UNIQUE]) {
            throw new Exception(sprintf('%s %s %s', $field, $data, 'already exist'));
        }
        
        return $class;
    }
}
