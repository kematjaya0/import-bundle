<?php

/**
 * This file is part of the web-scraping.
 */

namespace Kematjaya\ImportBundle\Builder;

use Kematjaya\ImportBundle\DataTransformer\DataTransformerInterface;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @package Kematjaya\ImportBundle\Builder
 * @license https://opensource.org/licenses/MIT MIT
 * @author  Nur Hidayatullah <kematjaya0@gmail.com>
 */
class DataTransformerBuilder implements DataTransformerBuilderInterface
{
    /**
     * 
     * @var Collection
     */
    private $transformer;
    
    public function __construct() 
    {
        $this->transformer = new ArrayCollection();
    }
    
    public function addTransformer(DataTransformerInterface $chart): DataTransformerBuilderInterface 
    {
        if (!$this->transformer->contains($chart)) {
            $this->transformer->add($chart);
        }
        
        return $this;
    }

    public function getTransformer(string $className): DataTransformerInterface 
    {
        $collection = $this->transformer->filter(function (DataTransformerInterface $chart) use ($className) {
            
            return $className === get_class($chart);
        });
        
        if ($collection->isEmpty()) {
            
            throw new \Exception("cannot find processor: ". $className);
        }
        
        return $collection->first();
    }
}
