<?php

namespace DataDog\AuditBundle\Controller;

use AppBundle\Util\DataTable\DataTableOptionsProcessor;
use AppBundle\Util\Rest\DataTableRestResponse;
use AppBundle\Util\Rest\RestResponse;
use DataDog\AuditBundle\Service\AuditLogService;
use JMS\Serializer\SerializationContext;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class AuditLogController
 * @package DataDog\AuditBundle\Controller
 * @Route("/")
 */
class AuditLogController extends Controller {

    // REST Methods
    /**
     * @Route("/rest/getAll", name="nti_rest_audit_log_get_all", options={"expose"=true}, methods={"GET"})
     * @param Request $request
     * @return DataTableRestResponse
     */
    public function getAllAction(Request $request) {
        $options = DataTableOptionsProcessor::GetOptions($request);
        $result = $this->get(AuditLogService::class)->getAll($options);
        $logs = json_decode($this->container->get('jms_serializer')->serialize($result["data"], 'json') ,true);
        $result["data"] = $logs;
        // dd($result["data"]);
        return new DataTableRestResponse($result);
    }

}