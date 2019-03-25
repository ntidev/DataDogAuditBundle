<?php
/**
 * Created by PhpStorm.
 * User: IMendoza
 * Date: 3/22/2019
 * Time: 9:59 AM
 */

namespace DataDog\AuditBundle\EventListener;

use DataDog\AuditBundle\Entity\AuditRequest;
use DataDog\AuditBundle\DBAL\AuditLogger;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ControllerListener
{
    /** @var ContainerInterface */
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function onKernelController(FilterControllerEvent $event){

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
}