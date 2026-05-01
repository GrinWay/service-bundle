<?php

namespace GrinWay\Service\Contract\Doctrine;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\QueryBuilder;

interface ActiveAndPriorityAwareRepositoryInterface
{
    public function findFirstActive(?Criteria $criteria = null): ?object;

    public static function firstActiveQb(QueryBuilder $qb): QueryBuilder;
}
