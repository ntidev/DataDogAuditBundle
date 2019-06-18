<?php

namespace DataDog\AuditBundle\Repository;

use DataDog\AuditBundle\Entity\Association;

class AssociationRepository extends \Doctrine\ORM\EntityRepository
{
    public function findAudit(\DateTime $dateStart, \DateTime $dateEnd){
        $qb = $this->createQueryBuilder('a');
        $qb ->select('a')
            ->andWhere('a.createdOn >= :dateStart')
            ->andWhere('a.createdOn <= :dateEnd')
            ->setParameter('dateStart', $dateStart)
            ->setParameter('dateEnd', $dateEnd);

        return $qb->getQuery()->getResult();
    }
}