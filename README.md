# Elastica specification bundle

This bundle integrates [elastica-specification](git@github.com:gbprod/elastica-specification.git) with Symfony.

[![Build Status](https://travis-ci.org/gbprod/elastica-specification-bundle.svg?branch=master)](https://travis-ci.org/gbprod/elastica-specification-bundle)
[![codecov](https://codecov.io/gh/gbprod/elastica-specification-bundle/branch/master/graph/badge.svg)](https://codecov.io/gh/gbprod/elastica-specification-bundle)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/gbprod/elastica-specification-bundle/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/gbprod/elastica-specification-bundle/?branch=master)
[![Dependency Status](https://www.versioneye.com/user/projects/574a9c9ace8d0e004130d337/badge.svg)](https://www.versioneye.com/user/projects/574a9c9ace8d0e004130d337)

[![Latest Stable Version](https://poser.pugx.org/gbprod/elastica-specification-bundle/v/stable)](https://packagist.org/packages/gbprod/elastica-specification-bundle)
[![Total Downloads](https://poser.pugx.org/gbprod/elastica-specification-bundle/downloads)](https://packagist.org/packages/gbprod/elastica-specification-bundle)
[![Latest Unstable Version](https://poser.pugx.org/gbprod/elastica-specification-bundle/v/unstable)](https://packagist.org/packages/gbprod/elastica-specification-bundle)
[![License](https://poser.pugx.org/gbprod/elastica-specification-bundle/license)](https://packagist.org/packages/gbprod/elastica-specification-bundle)

## Installation

Download bundle using [composer](https://getcomposer.org/) :

```bash
composer require gbprod/elastica-specification-bundle
```

Declare in your `app/AppKernel.php` file:

```php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new GBProd\ElasticaSpecificationBundle\ElasticaSpecificationBundle(),
        // ...
    );
}
```

## Create your specification and your query factory

Take a look to [Specification](https://github.com/gbprod/specification) and [Elastica Specification](https://github.com/gbprod/specification) libraries for more informations.

### Create a specification

```php
<?php

namespace GBProd\Acme\CoreDomain\Specification\Product;

use GBProd\Specification\Specification;

class IsAvailable implements Specification
{
    public function isSatisfiedBy($candidate)
    {
        return $candidate->isSellable()
            && $candidate->expirationDate() > new \DateTime('now')
        ;
    }
}
```

### Create a query factory

```php
<?php

namespace GBProd\Acme\Infrastructure\Elastica\QueryFactory\Product;

use GBProd\ElasticaSpecification\QueryFactory\Factory;
use GBProd\Specification\Specification;
use Elastica\QueryBuilder;

class IsAvailableFactory implements Factory
{
    public function build(Specification $spec, QueryBuilder $qb)
    {
        return $qb->query()->bool()
            ->addMust()
                $qb->query()->term(['available' => "0"]),
            )
        ;
    }
}
```

## Configuration

### Declare your Factory

```yaml
// src/GBProd/Acme/AcmeBundle/Resource/config/service.yml

services:
    acme.elastica.query_factory.is_available:
        class: GBProd\Acme\Infrastructure\Elastica\QueryFactory\Product\IsAvailableFactory
        tags:
            - { name: elastica.query_factory, specification: GBProd\Acme\CoreDomain\Specification\Product\IsAvailable }
```

### Inject handler in your repository class

```yaml
// src/GBProd/Acme/AcmeBundle/Resource/config/service.yml

services:
    acme.product_repository:
        class: GBProd\Acme\Infrastructure\Product\ElasticaProductRepository
        arguments:
            - "@elastica.client"
            - "@gbprod.elastica_specification_handler"
```

```php
<?php

namespace GBProd\Acme\Infrastructure\Product;

use Elastica\Client;
use Elastica\QueryBuilder;
use GBProd\ElasticaSpecification\Handler;
use GBProd\Specification\Specification;

class ElasticaProductRepository implements ProductRepository
{
    private $client;

    private $handler;

    public function __construct(Client $em, Handler $handler)
    {
        $this->client  = $client;
        $this->handler = $handler;
    }

    public function findSatisfying(Specification $specification)
    {
        $type = $this
            ->getIndex('catalog')
            ->getType('product')
        ;

        $query = $this->handler->handle($specification, new QueryBuilder());

        return $type->search($query);
    }
}
```

### Usage

```php
<?php

$products = $productRepository->findSatisfying(
    new AndX(
        new IsAvailable(),
        new IsLowStock()
    )
);
```

### Elastica configuration

You can specify the Elastica DSL version using `dsl_version` configuration node (default: `Latest`).
To use a different version, just set it to the [version classname](https://github.com/ruflin/Elastica/tree/master/lib/Elastica/QueryBuilder/Version).

```yaml
# app/config/config.yml
elastica_specification_bundle:
    dsl_version: Version120 
```
