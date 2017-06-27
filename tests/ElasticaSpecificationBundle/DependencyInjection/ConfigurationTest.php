<?php

namespace Tests\GBProd\ElasticaSpecificationBundle\DependencyInjection;

use GBProd\ElasticaSpecificationBundle\DependencyInjection\Configuration;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\Processor;

/**
 * Tests for Configuration
 *
 * @author gbprod <contact@gb-prod.fr>
 */
class ConfigurationTest extends TestCase
{
    private $configuration;

    public function setUp()
    {
        $this->configuration = new Configuration();
    }

    public function testEmptyConfiguration()
    {
        $processed = $this->process([]);

        $this->assertEquals([
            'dsl_version' => 'Latest'
        ], $processed);
    }

    protected function process(array $config)
    {
        $processor = new Processor();

        return $processor->processConfiguration(
            $this->configuration,
            $config
        );
    }

    public function testSetAValidBuilderVersion()
    {
        $processed = $this->process([
            [
                'dsl_version' => 'Version240',
            ]
        ]);

        $this->assertEquals([
            'dsl_version' => 'Version240'
        ], $processed);
    }

    public function testSetAnInvalidBuilderVersion()
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->process([
            [
                'dsl_version' => 'Fake',
            ]
        ]);
    }
}
