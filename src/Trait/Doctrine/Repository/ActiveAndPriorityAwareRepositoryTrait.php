<?php

namespace GrinWay\Service\Trait\Doctrine\Repository;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\QueryBuilder;

/**
 * Use together with traits for entity:
 *     GrinWay\Service\Trait\Doctrine\Active
 *     GrinWay\Service\Trait\Doctrine\Priority
 *
 * @author Grigory Koblitskiy <grin180898@outlook.com>
 */
trait ActiveAndPriorityAwareRepositoryTrait
{
    /**
     * Get first active entity (first means with the highest priority)
     */
    public function findFirstActive(?Criteria $criteria = null): ?object
    {
        $qb = static::firstActiveQb($this->createQueryBuilder('o'));

        if (null !== $criteria) {
            $qb->addCriteria($criteria);
        }

        return $qb
            ->getQuery()
            ->getOneOrNullResult()//
            ;
    }

    public static function firstActiveQb(QueryBuilder $qb): QueryBuilder
    {
        $alias = $qb->getRootAliases()[0];

        return $qb
            ->orderBy(
                \sprintf('%s.priority', $alias),
                'DESC',
            )
            ->andWhere(\sprintf('%s.active = TRUE', $alias))
            ->setMaxResults(1)//
            ;
    }
}
