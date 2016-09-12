<?php

namespace Tests\GBProd\ElasticaSpecificationBundle\DependencyInjection;

use Elastica\QueryBuilder;
use GBProd\ElasticaSpecificationBundle\DependencyInjection\ElasticaSpecificationExtension;
use GBProd\ElasticaSpecification\Handler;
use GBProd\ElasticaSpecification\Registry;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Tests for ElasticaSpecificationExtension
 *
 * @author gbprod <contact@gb-prod.fr>
 */
class ElasticaSpecificationExtensionTest extends \PHPUnit_Framework_TestCase
{
    private $extension;
    private $container;

    protected function setUp()
    {
        $this->extension = new ElasticaSpecificationExtension();

        $this->container = new ContainerBuilder();
        $this->container->registerExtension($this->extension);

        $this->container->loadFromExtension($this->extension->getAlias());
        $this->container->compile();
    }

    public function testLoadHasServices()
    {
        $this->assertTrue(
            $this->container->has('gbprod.elastica_specification_querybuilder')
        );

        $this->assertTrue(
            $this->container->has('gbprod.elastica_specification_registry')
        );

        $this->assertTrue(
            $this->container->has('gbprod.elastica_specification_handler')
        );
    }

    public function testLoadQueryBuilder()
    {
        $registry = $this->container->get('gbprod.elastica_specification_querybuilder');

        $this->assertInstanceOf(QueryBuilder::class, $registry);
    }

    public function testLoadRegistry()
    {
        $registry = $this->container->get('gbprod.elastica_specification_registry');

        $this->assertInstanceOf(Registry::class, $registry);
    }

    public function testLoadHandler()
    {
        $handler = $this->container->get('gbprod.elastica_specification_handler');

        $this->assertInstanceOf(Handler::class, $handler);
    }
}
