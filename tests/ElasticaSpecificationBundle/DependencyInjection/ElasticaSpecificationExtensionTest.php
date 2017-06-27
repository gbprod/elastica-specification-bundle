<?php

namespace Tests\GBProd\ElasticaSpecificationBundle\DependencyInjection;

use Elastica\QueryBuilder;
use Elastica\QueryBuilder\Version\Latest;
use Elastica\QueryBuilder\Version\Version240;
use GBProd\ElasticaSpecificationBundle\DependencyInjection\ElasticaSpecificationExtension;
use GBProd\ElasticaSpecification\Handler;
use GBProd\ElasticaSpecification\Registry;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Tests for ElasticaSpecificationExtension
 *
 * @author gbprod <contact@gb-prod.fr>
 */
class ElasticaSpecificationExtensionTest extends TestCase
{
    private $extension;
    private $container;

    protected function setUp()
    {
        $this->extension = new ElasticaSpecificationExtension();

        $this->container = new ContainerBuilder();
        $this->container->registerExtension($this->extension);
    }

    public function testLoadHasServices()
    {
        $this->load();

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

    private function load(array $config = [])
    {
        $this->container->loadFromExtension($this->extension->getAlias(), $config);
        $this->container->compile();
    }

    public function testLoadQueryBuilder()
    {
        $this->load();

        $registry = $this->container->get('gbprod.elastica_specification_querybuilder');

        $this->assertInstanceOf(QueryBuilder::class, $registry);
    }

    public function testLoadRegistry()
    {
        $this->load();

        $registry = $this->container->get('gbprod.elastica_specification_registry');

        $this->assertInstanceOf(Registry::class, $registry);
    }

    public function testLoadHandler()
    {
        $this->load();

        $handler = $this->container->get('gbprod.elastica_specification_handler');

        $this->assertInstanceOf(Handler::class, $handler);
    }

    public function testLoadDsl()
    {
        $this->load();

        $dsl = $this->container->get('gbprod.elastica_specification_dsl');

        $this->assertInstanceOf(Latest::class, $dsl);
    }

    public function testLoadCustomizedDsl()
    {
        $this->load([
            'dsl_version' => 'Version240',
        ]);

        $dsl = $this->container->get('gbprod.elastica_specification_dsl');

        $this->assertInstanceOf(Version240::class, $dsl);
    }
}
