<?php

/**
 * This file is part of the import-bundle.
 */

namespace Kematjaya\ImportBundle\DataSource;

use Exception;

/**
 * @package Kematjaya\ImportBundle\DataSource
 * @license https://opensource.org/licenses/MIT MIT
 * @author  Nur Hidayatullah <kematjaya0@gmail.com>
 */
class RemoteXmlDataSource extends RemoteDataSource
{
    /**
     * Execution from remote source to array
     * 
     * @return array
     * @throws Exception
     */
    public function execute(): array 
    {
        $resultset = [];
        try{
            $response = $this->getClient()->request(
                $this->getMethod(),
                $this->getUrl()
            );
            
            libxml_use_internal_errors(true);
            $result = simplexml_load_string($response->getContent());
            if (!empty(libxml_get_errors())) {
                throw new Exception(sprintf("invalid xml format from url: '%s'", $this->getUrl()));
            }
            
            return json_decode(json_encode($result), true);
        } catch (Exception $ex) {
            throw $ex;
        }
            
        return $resultset;
    }
}
