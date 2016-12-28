<?php

namespace GBProd\ElasticaSpecificationBundle\DependencyInjection;

use Elastica\QueryBuilder\Version;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * Configuration
 *
 * @author gbprod <contact@gb-prod.fr>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $root = $treeBuilder->root('elastica_specification_bundle');

        $root
            ->children()
                ->scalarNode('dsl_version')
                    ->defaultValue('Latest')
                    ->cannotBeEmpty()
                    ->beforeNormalization()
                        ->ifTrue(function($value) {
                            return !is_string($value)
                                || !class_exists('Elastica\\QueryBuilder\\Version\\'.$value)
                            ;
                        })
                        ->thenInvalid('QueryBuilder version "%s" does not exists')
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
