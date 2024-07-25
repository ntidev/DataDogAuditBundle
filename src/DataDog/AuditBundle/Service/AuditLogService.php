<?php

namespace DataDog\AuditBundle\Service;

use DataDog\AuditBundle\Repository\AuditLogRepository;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class AuditLogService
 * @package DataDog\AuditBundle\Service
 */
class AuditLogService {

    /** @var ContainerInterface $container */
    private $container;

    /** @var AuditLogRepository $auditLogRepository */
    private $auditLogRepository;

    /**
     * AuditLogService constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container, AuditLogRepository $auditLogRepository)
    {
        $this->container = $container;
        $this->auditLogRepository = $auditLogRepository;
    }

    /**
     * Get the list of AuditLogs based on the given $options
     *
     * @param $options
     * @return mixed
     */
    public function getAll($options = array()) {
        // $connectionName = $this->container->getParameter('nti_audit.database.connection_name');
        // $em = $this->container->get('doctrine')->getManager($connectionName);
        $logs = $this->auditLogRepository->findByOptions($options);
        return $logs;
    }
}