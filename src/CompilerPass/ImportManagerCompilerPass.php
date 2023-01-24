<?php

namespace Kematjaya\ImportBundle\CompilerPass;

use Kematjaya\ImportBundle\Controller\ImportControllerInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Description of ImportManagerCompilerPass
 *
 * @author apple
 */
class ImportManagerCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container):void 
    {
        $taggedServices = $container->findTaggedServiceIds(ImportControllerInterface::TAG_NAME);
        foreach (array_keys($taggedServices) as $className) {
            $container->findDefinition($className)->addMethodCall("setImportManager");
        }
        
    }
}
