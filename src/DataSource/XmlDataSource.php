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
class XmlDataSource extends SpreadSheetDataSource
{
    /**
     * Execution from spreadsheet file source to array
     * 
     * @return array
     * @throws Exception
     */
    public function execute(): array
    {
        libxml_use_internal_errors(true);
        $result = simplexml_load_file($this->getFileName());
        if (!empty(libxml_get_errors())) {
            throw new Exception(sprintf("invalid xml format from file: '%s'", $this->getFileName()));
        }

        return json_decode(json_encode($result), true);
    }
}
