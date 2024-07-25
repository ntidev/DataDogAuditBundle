<?php

namespace DataDog\AuditBundle\Repository;

use DataDog\AuditBundle\Entity\AuditLog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class AuditLogRepository extends ServiceEntityRepository
{
    const MAX_RESULTS = 1000;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AuditLog::class);
    }

    public function findAudit(\DateTime $dateStart, \DateTime $dateEnd){
        $qb = $this->createQueryBuilder('a');
        $qb ->select('a')
            ->andWhere('a.loggedAt >= :dateStart')
            ->andWhere('a.loggedAt <= :dateEnd')
            ->setParameter('dateStart', $dateStart)
            ->setParameter('dateEnd', $dateEnd);

        return $qb->getQuery()->getResult();
    }

    /**
     * Get a list of AuditLogs filtering by $options
     *
     * @param $options
     * @return array
     */
    public function findByOptions($options = array()) {
        $options = array(
            "draw" => isset($options["draw"]) ? $options["draw"] : 1,
            "search" => isset($options["search"]) ? $options["search"] : "",
            "sortBy" => isset($options["sortBy"]) ? $options["sortBy"] : "id",
            "orderBy" => (isset($options["orderBy"]) && in_array(strtolower($options["orderBy"]), array("desc", "asc"))) ? $options["orderBy"] : "asc",
            "start" => (isset($options["start"]) && $options["start"] >= 0) ? $options["start"] : 0,
            "limit" => (isset($options["limit"]) && $options["limit"] < self::MAX_RESULTS) ? $options["limit"] : ( (isset($options["length"]) && $options["length"] < self::MAX_RESULTS) ? $options["length"] : self::MAX_RESULTS ),
            "filters" => (isset($options["filters"]) && count($options["filters"]) > 0) ? $options["filters"] : array(),
            "partnerId" => (isset($options["partnerId"])) ? $options["partnerId"] : null,
            "partnerAuthorized" => (isset($options["partnerAuthorized"])) ? true : false,
            "_paginate" => (isset($options["_paginate"])) ? $options["_paginate"] : true,            
        );

        $qb = $this->createQueryBuilder('a')
            ->join('DataDog\AuditBundle\Entity\Association', 'aa', 'WITH', 'aa = a.source');
        
        // Apply filters
        foreach($options["filters"] as $field => $search) {
            if($field == "" || $search == "") continue;
            switch($field) {
                case "table":
                    switch($search) {
                        case "all":
                            $qb->andWhere($qb->expr()->andX(
                                $qb->expr()->neq('a.tbl', $qb->expr()->literal("audit_request")),
                                $qb->expr()->neq('a.tbl', $qb->expr()->literal("craue_config_setting")),
                                $qb->expr()->neq('a.tbl', $qb->expr()->literal("nti_sync_state"))
                            ));                                
                            break;
                        case "customer":
                            $qb->andWhere($qb->expr()->orX(
                                $qb->expr()->eq('a.tbl', $qb->expr()->literal("customer")),
                                $qb->expr()->eq('a.tbl', $qb->expr()->literal("customer_billing_profile")),
                                $qb->expr()->eq('a.tbl', $qb->expr()->literal("customer_product")),
                                $qb->expr()->eq('a.tbl', $qb->expr()->literal("customer_product_service_address")),
                                $qb->expr()->eq('a.tbl', $qb->expr()->literal("customer_tax_exemption_codes"))
                            ));
                            break;
                        case "invoice":
                            $qb->andWhere($qb->expr()->orX(
                                $qb->expr()->eq('a.tbl', $qb->expr()->literal("invoice")),
                                $qb->expr()->eq('a.tbl', $qb->expr()->literal("invoice_product"))
                            ));
                            break;
                        case "payment":
                            $qb->andWhere($qb->expr()->orX(
                                $qb->expr()->eq('a.tbl', $qb->expr()->literal("payment")),
                                $qb->expr()->eq('a.tbl', $qb->expr()->literal("payment_entry"))
                            ));
                            break;
                        case "users":
                            $qb->andWhere($qb->expr()->orX(
                                $qb->expr()->eq('a.tbl', $qb->expr()->literal("users")),
                                $qb->expr()->eq('a.tbl', $qb->expr()->literal("user_user_role_allowed"))
                            ));
                            break;
                        case "orders":
                            $qb->andWhere($qb->expr()->orX(
                                $qb->expr()->eq('a.tbl', $qb->expr()->literal("orders")),
                                $qb->expr()->eq('a.tbl', $qb->expr()->literal("order_product"))
                            ));
                            break;
                        default:
                            $qb->andWhere('a.tbl = :'.$field)->setParameter($field, $search);
                            break;
                    }                    
                    break;
                case "dateStart":
                    $qb->andWhere('a.loggedAt >= :'.$field)->setParameter($field, $search);
                    break;
                case "dateEnd":
                    $qb->andWhere('a.loggedAt <= :'.$field)->setParameter($field, $search);
                    break;
                case "user":
                    $qb->andWhere('aa.fk = :'.$field)->setParameter($field, $search);
                    break;
                case "action":
                    $qb->andWhere('a.action = :'.$field)->setParameter($field, $search);
                    break;
            }
        }

        // Manage SortBy
        if(is_array($options["sortBy"])) {
            foreach($options["sortBy"] as $sort) {
                $qb->addOrderBy($sort, "a.".$options["orderBy"]);
            }
        } else {
            $qb->orderBy($options["sortBy"], $options["orderBy"]);
        }

        $resultQuery = $qb->getQuery();
        if(isset($options["_paginate"]) && $options["_paginate"] == true) {
            if(null !== $options["limit"])
                $resultQuery->setMaxResults($options["limit"]);
            if(null !== $options["start"])
                $resultQuery->setFirstResult($options["start"]);
        }
        
        $logs = $resultQuery->getResult();

        return array(
            "draw" => intval( $options["draw"] ),
            'data' => $logs,
        );

    }
}