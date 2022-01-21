<?php

namespace Kematjaya\ImportBundle;

use Kematjaya\ImportBundle\DataTransformer\DataTransformerInterface;
use Kematjaya\ImportBundle\CompilerPass\DataTransformerCompilerPass;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author Nur Hidayatullah <kematjaya0@gmail.com>
 */
class ImportBundle extends Bundle
{
    public function build(ContainerBuilder $container) 
    {
        $container->registerForAutoconfiguration(DataTransformerInterface::class)
                ->addTag(DataTransformerInterface::TAG_NAME);
        
        $container->addCompilerPass(new DataTransformerCompilerPass());
        
        parent::build($container);
    }
}
