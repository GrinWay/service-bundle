<?php

namespace GrinWay\Service\Trait\Doctrine\Repository;

use Doctrine\Common\Collections\Criteria;

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
        $qb = $this->createQueryBuilder('o')
            ->orderBy('o.priority', 'DESC')
            ->andWhere('o.active = TRUE')//
        ;

        if (null !== $criteria) {
            $qb->addCriteria($criteria);
        }

        return $qb
            ->getQuery()
            ->getOneOrNullResult()//
            ;
    }
}
