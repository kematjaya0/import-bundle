<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPInterface.php to edit this template
 */

namespace Kematjaya\ImportBundle\Controller;

use Kematjaya\ImportBundle\Manager\ImportManagerInterface;

/**
 *
 * @author apple
 */
interface ImportControllerInterface 
{
    
    const TAG_NAME = 'controller.import_manager_controller';
    
    public function setImportManager(ImportManagerInterface $importManager):void;
    
    public function getImportManager():ImportManagerInterface;
}
