<?php

namespace Kematjaya\ImportBundle\Builder;

use Kematjaya\ImportBundle\DataTransformer\DataTransformerInterface;

/**
 * @package Kematjaya\ImportBundle\Builder
 * @license https://opensource.org/licenses/MIT MIT
 * @author  Nur Hidayatullah <kematjaya0@gmail.com>
 */
interface DataTransformerBuilderInterface 
{
    public function addTransformer(DataTransformerInterface $transformer): self;

    public function getTransformer(string $className): DataTransformerInterface;
}
