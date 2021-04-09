<?php

namespace DataDog\AuditBundle\EventListener;

use Doctrine\Common\Annotations\AnnotationReader;
use ReflectionObject;
use DataDog\AuditBundle\Entity\AuditRequest;
use DataDog\AuditBundle\DBAL\AuditLogger;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\DependencyInjection\ContainerInterface;
use DataDog\AuditBundle\Annotations\NTIAudit;
use Exception;

class ControllerListener
{
    /** @var ContainerInterface */
    protected $container;
    protected $unauditedRequestFieldsPath = [];

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function addUnauditedRequestFields(array $unauditedRequestFields)
    {
        $this->unauditedRequestFieldsPath = $unauditedRequestFields;
    }

    public function onKernelController(FilterControllerEvent $event){

        if(!$this->container->getParameter('nti_audit.audit_request.enabled')){
            return;
        }

        $this->addUnauditedRequestFields($this->container->getParameter('nti_audit.audit_request.unaudited_request_fields'));

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

        // Get custom connection for logging
        $connectionName = $this->container->getParameter('nti_audit.database.connection_name');
        $em = $this->container->get('doctrine')->getManager($connectionName);

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
        foreach ($this->unauditedRequestFieldsPath as $unauditedPath)
            $data = $this->removeJsonField($unauditedPath, $data);

        $app_name = $this->container->getParameter('app_short_name');
        
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
        $audit->setAppName($app_name);
        $audit->setCreatedOn(new \DateTime());

        $em->persist($audit);

        try{
            $em->flush();
        }catch (\Exception $ex){
            $fp = fopen('errors.txt', 'w');
            fwrite($fp, $ex);
            fclose($fp);
        }
    }

    public function removeJsonField($path, $data) {
        
        $pathKeys = explode(".", $path);
        $unauditedField = $pathKeys[count($pathKeys) - 1];

        // Remove '$' from keys
        array_shift($pathKeys);

        if(!is_string($data) || !$this->is_json($data) || !preg_match('/\b'.$unauditedField.'\b/', $data))
            return $data;

        $data = json_decode($data, true);
        $data = $this->searchAndRemoveField($pathKeys, $data);
        $data = json_encode($data);
        return $data;
    }

    public function searchAndRemoveField($pathKeys, $data){
        $field = &$data;
        $pathKeysLeft = $pathKeys;
        foreach ($pathKeys as $key) {
            try{
                if(is_array($field[$key]) && array_values($field[$key]) === $field[$key]){
                    array_shift($pathKeysLeft);
                    foreach ($field[$key] as $arrKey => $arr) {
                        $field[$key][$arrKey] = $this->searchAndRemoveField($pathKeysLeft, $arr);
                    }
                    return $data;
                } else {
                    array_shift($pathKeysLeft);
                }
                $field = &$field[$key];
            } catch (Exception $ex){
                return $data;
            }
        }
        $field = "*";
        return $data;
    }

    public function is_json($string) {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE) ? true : false;
    }
}