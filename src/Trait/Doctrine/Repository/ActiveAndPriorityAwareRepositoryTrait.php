<?php

namespace GrinWay\Service\Trait\Doctrine\Repository;

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
    public function findFirstActive(): ?object
    {
        return $this->createQueryBuilder('o')
            ->orderBy('o.priority', 'DESC')
            ->andWhere('o.active = TRUE')
            ->getQuery()
            ->getOneOrNullResult()//
            ;
    }
}
