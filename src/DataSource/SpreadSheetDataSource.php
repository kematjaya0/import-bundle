<?php

/**
 * This file is part of Kematjaya\ImportBundle
 */

namespace Kematjaya\ImportBundle\DataSource;

use Exception;
use Symfony\Component\Filesystem\Filesystem;
use PhpOffice\PhpSpreadsheet\IOFactory;

/**
 * Processing from spreadsheet to array
 * 
 * @category Kematjaya\ImportBundle
 * @package  Kematjaya\ImportBundle\Manager
 * @license  https://opensource.org/licenses/MIT MIT
 * @author   Nur Hidayatullah <kematjaya0@gmail.com>
 */
class SpreadSheetDataSource extends AbstractDataSource
{
    /**
     *
     * @var string
     */
    private $fileName;
    
    /**
     *
     * @var Filesystem 
     */
    private $fileSystem;
    
    public function __construct(string $fileName) 
    {
        $fileSystem = new Filesystem();
        
        if (!$fileSystem->exists($fileName)) {
            throw new Exception('file ' . $fileName .' not exist');
        }
        
        $this->fileName = $fileName;
        $this->fileSystem = $fileSystem;
    }
    
    public function getFileName():string
    {
        return $this->fileName;
    }
    
    /**
     * Execution from spreadsheet file source to array
     * 
     * @return array
     * @throws Exception
     */
    public function execute(): array 
    {
        try 
        {
            $reader         = (IOFactory::createReader('Xlsx'))->setReadDataOnly(true);
            $spreadsheet    = $reader->load($this->getFileName());

            return ($spreadsheet->getActiveSheet()) ? $spreadsheet->getActiveSheet()->toArray():[];
            
        } catch (\Exception $ex) 
        {
            throw $ex;
        }
    }

}
