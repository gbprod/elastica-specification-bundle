<?php

namespace Tests\GBProd\ElasticaSpecificationBundle;

use GBProd\ElasticaSpecificationBundle\ElasticaSpecificationBundle;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Tests for ElasticaSpecificationBundle
 *
 * @author GBProd <contact@gb-prod.fr>
 */
class ElasticaSpecificationBundleTest extends \PHPUnit_Framework_TestCase
{
    public function testConstruction()
    {
        $bundle = new ElasticaSpecificationBundle();

        $this->assertInstanceOf(ElasticaSpecificationBundle::class, $bundle);
        $this->assertInstanceOf(Bundle::class, $bundle);
    }
}
