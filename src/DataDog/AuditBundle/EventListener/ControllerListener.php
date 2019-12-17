<?php

namespace DataDog\AuditBundle\EventListener;

use Doctrine\Common\Annotations\AnnotationReader;
use ReflectionObject;
use DataDog\AuditBundle\Entity\AuditRequest;
use DataDog\AuditBundle\DBAL\AuditLogger;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\DependencyInjection\ContainerInterface;
use DataDog\AuditBundle\Annotations\NTIAudit;

class ControllerListener
{
    /** @var ContainerInterface */
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function onKernelController(FilterControllerEvent $event){

        if(!$this->container->getParameter('nti_audit.audit_request.enabled')){
            return;
        }

        if (!is_array($controllers = $event->getController())) {
            return;
        }

        list($controller, $methodName) = $controllers;
        $reflectionClass = new \ReflectionClass($controller);

        // Controller
        $reader = new \Doctrine\Common\Annotations\AnnotationReader();
        $classAnnotation = $reader->getClassAnnotation($reflectionClass, NTIAudit::class);

        // Method
        $reflectionMethod = $reflectionClass->getMethod($methodName);
        $methodAnnotation = $reader->getMethodAnnotation($reflectionMethod, NTIAudit::class);

        if(!($classAnnotation || $methodAnnotation)){
            return;
        }

        // Get Request
        $request = $event->getRequest();

        if(strpos($request->attributes->get('_controller'), 'render')){
            return;
        }

        $em = $this->container->get('doctrine')->getManager();

        // User Loggued
        $user = '';
        if (null === $token = $this->container->get('security.token_storage')->getToken()){
            $user = '';
        }

        /** @var User $user */
        if($token) {
            if (!is_object($user = $token->getUser())) {
                $user = '';
            } elseif ($token->getUser()) {
                if ($token->getUser()->getUserName()) {
                    $user = $token->getUser()->getUserName();
                }
            }
        }

        // Get Data
        $method = $request->getMethod();
        $controller = $request->attributes->get('_controller');
        $route = $request->attributes->get('_route');
        $routeParams = $request->attributes->get('_route_params') ? json_encode($request->attributes->get('_route_params')) : '';
        $ip = count($request->getClientIps()) > 0 ? $request->getClientIps()[0] : '';

        $userName = $request->headers->get('userName') ?? '';

        if($userName){
            $user = $userName;
        }

        $userName = $user;
        $portal =$request->headers->get('portal') ?? '';
        $queryData = $request->getQueryString();
        $data = $request->getContent();

        // Filter sensitive data
        if (is_string($data) && (preg_match('/\bcreditCard\b/', $data) || preg_match('/\bcredit_card\b/', $data))) {
            $decoded = json_decode($data, true);
            $decoded = $this->filterRecursive($decoded);
            $data = json_encode($decoded);
        }

        // Set Object
        $audit = new AuditRequest();
        $audit->setMethod($method);
        $audit->setController($controller);
        $audit->setRoute($route);
        $audit->setRouteParams($routeParams);
        $audit->setIp($ip);
        $audit->setUserName($userName);
        $audit->setPortal($portal);
        $audit->setQueryData($queryData);
        $audit->setData($data);
        $audit->setCreatedOn(new \DateTime());

        $em->persist($audit);

        try{
            $em->flush();
        }catch (\Exception $ex){

        }
    }

    function filterRecursive($array){
        foreach($array as $key => $value){
            if($key == "creditCard" || $key == "credit_card"){
                $value = "";
                return $value;
            }
            if(is_array($value)){
                $array[$key] = $this->filterRecursive($value);
            } 
        }
        return $array;
    }
}