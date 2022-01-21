<?php

/**
 * This file is part of the web-scraping.
 */

namespace Kematjaya\ImportBundle\CompilerPass;

use Kematjaya\ImportBundle\Builder\DataTransformerBuilderInterface;
use Kematjaya\ImportBundle\DataTransformer\DataTransformerInterface;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

/**
 * @package Kematjaya\ImportBundle\DataTransformerCompilerPass
 * @license https://opensource.org/licenses/MIT MIT
 * @author  Nur Hidayatullah <kematjaya0@gmail.com>
 */
class DataTransformerCompilerPass implements CompilerPassInterface
{
    
    public function process(ContainerBuilder $container) 
    {
        $definition = $container->findDefinition(DataTransformerBuilderInterface::class);
        $taggedServices = $container->findTaggedServiceIds(DataTransformerInterface::TAG_NAME);
        foreach ($taggedServices as $id => $tags) {
            $definition->addMethodCall('addTransformer', [new Reference($id)]);
        }
    }

}
