<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPInterface.php to edit this template
 */

namespace Kematjaya\ImportBundle\DataTransformer;

/**
 *
 * @author apple
 */
interface FormTransformerInterface 
{
    /**
     * 
     * @param type $data
     * @return array|object
     */
    public function setFormData($data):self;
}
