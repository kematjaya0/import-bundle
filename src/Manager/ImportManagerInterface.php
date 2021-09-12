<?php

namespace Kematjaya\ImportBundle\Manager;

use Kematjaya\ImportBundle\DataSource\AbstractDataSource;
use Kematjaya\ImportBundle\DataTransformer\AbstractDataTransformer;
use Doctrine\Common\Collections\Collection;


/**
 * Manager for process any format data to array and persisted into database
 *
 * @category Manager
 * @license  https://opensource.org/licenses/MIT MIT
 * @author   Nur Hidayatullah <kematjaya0@gmail.com>
 */
interface ImportManagerInterface
{
    /**
     * Function for processing data into database
     * 
     * @param AbstractDataSource $source
     * @param AbstractDataTransformer $transformer
     * @param array $options
     * @param callable $validate
     * @return Collection
     */
    public function process(AbstractDataSource $source, AbstractDataTransformer $transformer, array $options = [], callable $validate = null):Collection;
}
