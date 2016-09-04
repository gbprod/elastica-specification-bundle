<?php

namespace GBProd\ElasticaSpecificationBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Register elastica query factories
 *
 * @author GBProd <contact@gb-prod.fr>
 */
class QueryFactoryPass implements CompilerPassInterface
{
    /**
     * {inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('gbprod.elastica_specification_handler')) {
            throw new \Exception('Missing gbprod.elastica_specification_handler definition');
        }

        $handler = $container->findDefinition('gbprod.elastica_specification_handler');

        $factories = $container->findTaggedServiceIds('elastica.query_factory');

        foreach ($factories as $id => $tags) {
            foreach ($tags as $attributes) {
                if (!isset($attributes['specification'])) {
                    throw new \Exception(
                        'The elastica.query_factory tag must always have a "specification" attribute'
                    );
                }

                $handler->addMethodCall(
                    'registerFactory',
                    [$attributes['specification'], new Reference($id)]
                );
            }
        }
    }
}
