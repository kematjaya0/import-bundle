<?php

namespace Kematjaya\ImportBundle\Controller;

use Kematjaya\ImportBundle\DataSource\SpreadSheetDataSource;
use Kematjaya\ImportBundle\Manager\ImportManagerInterface;
use Kematjaya\ImportBundle\DataTransformer\AbstractDataTransformer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Form\FormInterface;
use Doctrine\Common\Collections\Collection;

/**
 * @author Nur Hidayatullah <kematjaya0@gmail.com>
 */
trait ImportControllerTrait 
{
    protected $importManager;
    
    protected function setImportManager(ImportManagerInterface $importManager):void
    {
        $this->importManager = $importManager;
    }
    
    protected function doSpreadsheetImport(
            FormInterface $form, 
            Request $request,
            AbstractDataTransformer $transformer,
            $fieldName = 'attachment')
    {
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if($form->isValid()) {
                $file = $form[$fieldName]->getData();
                if($file instanceof File) {
                    try {
                        $source = (new SpreadSheetDataSource($file->getRealPath()))->setReadedRow((int)$this->getParameter('spreadsheet.start_row'));
                                
                        $resultsets = $this->importManager->process($source, $transformer);
                        
                        return $this->buildImportSuccessResult($resultSets);
                    } catch (\Exception $ex) {
                        return $this->buildImportErrorResult($ex->getMessage());
                    }
                }
            } else {
                $errors = $this->getErrorsFromForm($form);
                
                return $this->buildImportErrorResult(implode(", ", $errors));
            }
        }
        
        return ['process' => false];
    }
    
    protected function buildImportErrorResult(string $message)
    {
        return [
            'process' => true, 
            'status' => false, 
            'errors' => true,
            'errors' => $message
        ];
    }
    
    protected function buildImportSuccessResult(Collection $resultSets)
    {
        return [
            "process" => true, 
            "status" => true, 
            "errors" => null, 
            "message" => sprintf('import successfull (%s data)', $resultSets->count())];
    }
}
