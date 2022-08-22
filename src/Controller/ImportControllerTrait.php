<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPTrait.php to edit this template
 */

namespace Kematjaya\ImportBundle\Controller;

use Kematjaya\ImportBundle\DataSource\SpreadSheetDataSource;
use Kematjaya\ImportBundle\DataTransformer\FormTransformerInterface;
use Kematjaya\ImportBundle\Manager\ImportManagerInterface;
use Kematjaya\ImportBundle\DataTransformer\AbstractDataTransformer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Form\FormInterface;
use Countable;

/**
 *
 * @author apple
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
    public function setImportManager(ImportManagerInterface $importManager):void
    {
        $this->importManager = $importManager;
    }
    
    public function getImportManager():ImportManagerInterface 
    {
        return $this->importManager;
    }
    
    protected function doSpreadsheetImport(FormInterface $form, Request $request, AbstractDataTransformer $transformer, string $fieldName = 'attachment', callable $validator = null): array
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
            throw new \Exception(
                sprintf("%s must instance of %s", $fieldName, File::class)
            );
        }
        
        try {
            return $this->processFile(
                $file, 
                $transformer, 
                $form->getData(), 
                $validator
            );
        } catch (\Exception $ex) {

            return $this->buildImportErrorResult(
                $ex->getMessage()
            );
        }
        
        throw new \Exception(
            sprintf("unable to process form")
        );
    }
    
    protected function processFile(File $file, AbstractDataTransformer $transformer, $data, callable $validator = null):array
    {
        if ($transformer instanceof FormTransformerInterface) {
            $transformer->setFormData($data);
        }
        
        $source = (new SpreadSheetDataSource($file->getRealPath()))
                ->setReadedRow((int)$this->getParameter('spreadsheet.start_row'));
        $resultsets = $this->importManager->process(
            $source, 
            $transformer, 
            ["formData" => $data], 
            $validator
        );

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
     * @param  Countable $resultsets
     * @return array
     */
    protected function buildImportSuccessResult(Countable $resultsets):array
    {
        return [
            "process" => true, 
            "status" => true, 
            "errors" => null, 
            "message" => sprintf('import successfull (%s data)', $resultsets->count())
        ];
    }
}
