<?php

namespace App\Filter;

use App\Entity\User;
use Doctrine\ORM\QueryBuilder;

class UserByGroupIdFilter implements FilterInterface
{
    /**
     * @param QueryBuilder $queryBuilder
     * @param array $searchParams
     * @return bool
     */
    public function support(QueryBuilder $queryBuilder, array $searchParams): bool
    {
        return in_array(User::class, $queryBuilder->getRootEntities()) && isset($searchParams['groupId']);
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param array $searchParams
     * @return void
     */
    public function processQueryBuilder(QueryBuilder $queryBuilder, array $searchParams): void
    {
        $queryBuilder
            ->andWhere(':group_id MEMBER OF e.groups')
            ->setParameter('group_id', $searchParams['groupId']);
    }
}
