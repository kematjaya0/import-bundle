<?php

namespace Kematjaya\ImportBundle\Manager;

use Kematjaya\ImportBundle\DataSource\AbstractDataSource;
use Kematjaya\ImportBundle\DataTransformer\AbstractDataTransformer;
use Doctrine\Common\Collections\Collection;
/**
 * @author Nur Hidayatullah <kematjaya0@gmail.com>
 */
interface ImportManagerInterface 
{
    public function process(AbstractDataSource $source, AbstractDataTransformer $transformer):Collection;
}
