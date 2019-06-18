<?php

namespace DataDog\AuditBundle\Repository;

use DataDog\AuditBundle\Entity\AuditLog;

class AuditLogRepository extends \Doctrine\ORM\EntityRepository
{
    public function findAudit(\DateTime $dateStart, \DateTime $dateEnd){
        $qb = $this->createQueryBuilder('a');
        $qb ->select('a')
            ->andWhere('a.loggedAt >= :dateStart')
            ->andWhere('a.loggedAt <= :dateEnd')
            ->setParameter('dateStart', $dateStart)
            ->setParameter('dateEnd', $dateEnd);

        return $qb->getQuery()->getResult();
    }
}