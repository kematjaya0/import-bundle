<?php

namespace Kematjaya\ImportBundle;

use Kematjaya\ImportBundle\CompilerPass\ImportManagerCompilerPass;
use Kematjaya\ImportBundle\Controller\ImportControllerInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author Nur Hidayatullah <kematjaya0@gmail.com>
 */
class ImportBundle extends Bundle
{
    public function build(ContainerBuilder $container) 
    {
        $container->registerForAutoconfiguration(ImportControllerInterface::class)
                ->addTag(ImportControllerInterface::TAG_NAME);
        
        $container->addCompilerPass(new ImportManagerCompilerPass());
        
        parent::build($container);
    }
}
