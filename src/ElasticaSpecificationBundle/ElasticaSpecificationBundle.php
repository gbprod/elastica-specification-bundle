<?php

namespace GBProd\ElasticaSpecificationBundle;

use GBProd\ElasticaSpecificationBundle\DependencyInjection\Compiler\QueryFactoryPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * ElasticaSpecificationBundle
 *
 * @author GBProd <contact@gb-prod.fr>
 */
class ElasticaSpecificationBundle extends Bundle
{
    /**
     * {inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new QueryFactoryPass());
    }
}
