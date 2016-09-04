# Elastica specification bundle

This bundle integrates [elastica-specification](git@github.com:gbprod/elastica-specification.git) with Symfony.

[![Build Status](https://travis-ci.org/gbprod/elastica-specification-bundle.svg?branch=master)](https://travis-ci.org/gbprod/elastica-specification-bundle)
[![codecov](https://codecov.io/gh/gbprod/elastica-specification-bundle/branch/master/graph/badge.svg)](https://codecov.io/gh/gbprod/elastica-specification-bundle)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/gbprod/elastica-specification-bundle/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/gbprod/elastica-specification-bundle/?branch=master)
[![Dependency Status](https://www.versioneye.com/user/projects/574a9c9ace8d0e004130d337/badge.svg)](https://www.versioneye.com/user/projects/574a9c9ace8d0e004130d337)

[![Latest Stable Version](https://poser.pugx.org/gbprod/elastica-specification-bundle/v/stable)](https://packagist.org/packages/gbprod/elastica-specification)
[![Total Downloads](https://poser.pugx.org/gbprod/elastica-specification-bundle/downloads)](https://packagist.org/packages/gbprod/elastica-specification)
[![Latest Unstable Version](https://poser.pugx.org/gbprod/elastica-specification-bundle/v/unstable)](https://packagist.org/packages/gbprod/elastica-specification)
[![License](https://poser.pugx.org/gbprod/elastica-specification-bundle/license)](https://packagist.org/packages/gbprod/elastica-specification)

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

## Create your specification and your expression builder

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

### Create an expression builder

```php
<?php

namespace GBProd\Acme\Infrastructure\Elastica\ExpressionBuilder\Product;

use GBProd\ElasticaSpecification\ExpressionBuilder\Builder;
use GBProd\Specification\Specification;
use Elastica\QueryBuilder;

class IsAvailableBuilder implements Builder
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

### Declare your Builder

```yaml
// src/GBProd/Acme/AcmeBundle/Resource/config/service.yml

services:
    acme.elastica.expression_builder.is_available:
        class: GBProd\Acme\Infrastructure\Elastica\ExpressionBuilder\Product\IsAvailableBuilder
        tags:
            - { name: elastica.expression_builder, specification: GBProd\Acme\CoreDomain\Specification\Product\IsAvailable }
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
