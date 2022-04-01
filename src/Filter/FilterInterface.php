<?php

namespace App\Filter;

use Doctrine\ORM\QueryBuilder;

interface FilterInterface
{
    public function support(QueryBuilder $queryBuilder, array $searchParams): bool;

    public function processQueryBuilder(QueryBuilder $queryBuilder, array $searchParams): void;
}
