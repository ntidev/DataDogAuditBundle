<?php

namespace DataDog\AuditBundle\Service;

use DataDog\AuditBundle\Entity\AuditLog;
use PHPUnit\Runner\Exception;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class AuditLogService
 * @package DataDog\AuditBundle\Service
 */
class AuditLogService {

    /** @var ContainerInterface $container */
    private $container;

    /**
     * AuditLogService constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Get the list of AuditLogs based on the given $options
     *
     * @param $options
     * @return mixed
     */
    public function getAll($options = array()) {
        $em = $this->container->get('doctrine')->getManager();
        $logs = $em->getRepository(AuditLog::class)->findByOptions($options);
        return $logs;
    }

}