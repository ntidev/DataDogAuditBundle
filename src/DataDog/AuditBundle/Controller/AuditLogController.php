<?php

namespace DataDog\AuditBundle\Controller;

use DataDog\AuditBundle\Util\DataTable\DataTableOptionsProcessor;
use DataDog\AuditBundle\Util\Rest\DataTableRestResponse;
use DataDog\AuditBundle\Service\AuditLogService;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class AuditLogController
 * @package DataDog\AuditBundle\Controller
 * @Route("/")
 */
class AuditLogController extends AbstractController {

    /** @var ContainerInterface */
    protected $container;

    /** @var AuditLogService */
    protected $auditLogService;

    /** @var SerializerInterface */
    protected $serializer;

    public function __construct(ContainerInterface $container, AuditLogService $auditLogService, SerializerInterface $serializer)
    {
        $this->container = $container;
        $this->auditLogService = $auditLogService;
        $this->serializer = $serializer;
    }
    
    // REST Methods
    /**
     * @Route("/rest/getAll", name="nti_rest_audit_log_get_all", options={"expose"=true}, methods={"GET"})
     * @param Request $request
     * @return DataTableRestResponse
     */
    public function getAllAction(Request $request) {
        $options = DataTableOptionsProcessor::GetOptions($request);
        $result = $this->auditLogService->getAll($options);
        $logs = json_decode($this->serializer->serialize($result["data"], 'json') ,true);
        $result["data"] = $logs;
        return new DataTableRestResponse($result);
    }

}
