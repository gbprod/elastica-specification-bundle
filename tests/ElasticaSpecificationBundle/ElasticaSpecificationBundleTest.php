<?php

namespace Tests\GBProd\ElasticaSpecificationBundle;

use GBProd\ElasticaSpecificationBundle\DependencyInjection\Compiler\QueryFactoryPass;
use GBProd\ElasticaSpecificationBundle\ElasticaSpecificationBundle;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Tests for ElasticaSpecificationBundle
 *
 * @author GBProd <contact@gb-prod.fr>
 */
class ElasticaSpecificationBundleTest extends TestCase
{
    public function testConstruction()
    {
        $bundle = new ElasticaSpecificationBundle();

        $this->assertInstanceOf(ElasticaSpecificationBundle::class, $bundle);
        $this->assertInstanceOf(Bundle::class, $bundle);
    }

    public function testBuildAddCompilerPass()
    {
        $container = $this->prophesize(ContainerBuilder::class);
        $container
            ->addCompilerPass(new QueryFactoryPass())
            ->shouldBeCalled()
        ;

        $bundle = new ElasticaSpecificationBundle();
        $bundle->build($container->reveal());
    }
}
