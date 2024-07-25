<?php

namespace DataDog\AuditBundle\Repository;

use DataDog\AuditBundle\Entity\AuditRequest;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class AuditRequestRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AuditRequest::class);
    }

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