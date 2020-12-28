<?php

/**
 * This file is part of Kematjaya\ImportBundle
 */

namespace Kematjaya\ImportBundle\Controller;

use Kematjaya\ImportBundle\DataSource\SpreadSheetDataSource;
use Kematjaya\ImportBundle\Manager\ImportManagerInterface;
use Kematjaya\ImportBundle\DataTransformer\AbstractDataTransformer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Form\FormInterface;
use Doctrine\Common\Collections\Collection;

/**
 * @category Kematjaya\ImportBundle
 * @package  Kematjaya\ImportBundle\Manager
 * @license  https://opensource.org/licenses/MIT MIT
 * @author   Nur Hidayatullah <kematjaya0@gmail.com>
 */
trait ImportControllerTrait
{
    /**
     * 
     * @var ImportManagerInterface
     */
    protected $importManager;
    
    /**
     * Set ImportManager object
     * 
     * @param  ImportManagerInterface $importManager
     * @return void
     */
    protected function setImportManager(ImportManagerInterface $importManager):void
    {
        $this->importManager = $importManager;
    }
    
    /**
     * Processing file upload to ImportManagerInterface
     * 
     * @param  FormInterface           $form
     * @param  Request                 $request
     * @param  AbstractDataTransformer $transformer
     * @param  string                  $fieldName
     * @return array
     * @throws \Exception
     */
    protected function doSpreadsheetImport(FormInterface $form, Request $request, AbstractDataTransformer $transformer, string $fieldName = 'attachment'): array 
    {
        $form->handleRequest($request);
        if (!$form->isSubmitted()) {
            return ['process' => false];
        }
        
        if (!$form->isValid()) {
            $errors = $this->getErrorsFromForm($form);

            return $this->buildImportErrorResult(implode(", ", $errors));
        }
        
        $file = $form[$fieldName]->getData();
        if (!$file instanceof File) {
            throw new \Exception(sprintf("%s must instance of %s", $fieldName, File::class));
        }

        try {

            return $this->processFile($file, $transformer);
        } catch (\Exception $ex) {

            return $this->buildImportErrorResult($ex->getMessage());
        }
        
        throw new \Exception(sprintf("unable to process form"));
    }
    
    /**
     * Process spreadsheet file to ImportManager
     * 
     * @param  File                    $file
     * @param  AbstractDataTransformer $transformer
     * @return array
     */
    protected function processFile(File $file, AbstractDataTransformer $transformer):array
    {
        $source = (new SpreadSheetDataSource($file->getRealPath()))->setReadedRow((int)$this->getParameter('spreadsheet.start_row'));
        $resultsets = $this->importManager->process($source, $transformer);

        return $this->buildImportSuccessResult($resultsets);
    }
    
    /**
     * Import error result
     *  
     * @param  string $message
     * @return array
     */
    protected function buildImportErrorResult(string $message):array
    {
        return [
            'process' => true, 
            'status' => false, 
            'errors' => true,
            'errors' => $message
        ];
    }
    
    /**
     * Import success result
     * 
     * @param  Collection $resultsets
     * @return array
     */
    protected function buildImportSuccessResult(Collection $resultsets):array
    {
        return [
            "process" => true, 
            "status" => true, 
            "errors" => null, 
            "message" => sprintf('import successfull (%s data)', $resultsets->count())];
    }
}
