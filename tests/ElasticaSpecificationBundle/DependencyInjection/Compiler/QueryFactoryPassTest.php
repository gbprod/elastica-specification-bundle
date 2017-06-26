<?php

namespace Tests\GBProd\ElasticaSpecificationBundle\DependencyInjection\Compiler;

use GBProd\ElasticaSpecificationBundle\DependencyInjection\Compiler\QueryFactoryPass;
use GBProd\ElasticaSpecification\Handler;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Tests for QueryFactoryPass
 *
 * @author gbprod <contact@gb-prod.fr>
 */
class QueryFactoryPassTest extends TestCase
{
    public function testThrowExceptionIfNoHandlerDefinition()
    {
        $pass = new QueryFactoryPass();

        $this->expectException(\Exception::class);

        $pass->process(new ContainerBuilder());
    }

    public function testDoNothingIfNoTaggedServices()
    {
        $pass = new QueryFactoryPass();
        $container = $this->createContainerWithHandler();

        $pass->process($container);

        $calls = $container
            ->getDefinition('gbprod.elastica_specification_handler')
            ->getMethodCalls()
        ;

        $this->assertEmpty($calls);
    }

    private function createContainerWithHandler()
    {
        $container = new ContainerBuilder();

        $container->setDefinition(
            'gbprod.elastica_specification_handler',
            new Definition(Handler::class)
        );

        return $container;
    }

    public function testThrowExceptionIfTagHasNoSpecification()
    {
        $pass = new QueryFactoryPass();

        $container = $this->createContainerWithHandler();
        $container
            ->register('factory', \stdClass::class)
            ->addTag('elastica.query_factory')
        ;

        $this->expectException(\Exception::class);
        $pass->process($container);
    }

    public function testAddMethodCalls()
    {
        $pass = new QueryFactoryPass();

        $container = $this->createContainerWithHandler();
        $container
            ->register('factory1', 'Factory1')
            ->addTag('elastica.query_factory', ['specification' => 'Specification1'])
        ;

        $container
            ->register('factory2', 'Factory2')
            ->addTag('elastica.query_factory', ['specification' => 'Specification2'])
        ;

        $pass->process($container);

        $calls = $container
            ->getDefinition('gbprod.elastica_specification_handler')
            ->getMethodCalls()
        ;

        $this->assertCount(2, $calls);

        $this->assertEquals('registerFactory', $calls[0][0]);
        $this->assertEquals('Specification1', $calls[0][1][0]);
        $this->assertInstanceOf(Reference::class, $calls[0][1][1]);
        $this->assertEquals('factory1', $calls[0][1][1]);


        $this->assertEquals('registerFactory', $calls[1][0]);
        $this->assertEquals('Specification2', $calls[1][1][0]);
        $this->assertInstanceOf(Reference::class, $calls[1][1][1]);
        $this->assertEquals('factory2', $calls[1][1][1]);
    }
}
