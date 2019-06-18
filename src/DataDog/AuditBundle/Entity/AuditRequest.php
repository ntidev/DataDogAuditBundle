<?php

namespace DataDog\AuditBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="audit_request")
 * @ORM\Entity(repositoryClass="DataDog\AuditBundle\Repository\AuditRequestRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class AuditRequest
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="method", type="string", length=255, nullable=true)
     */
    private $method;

    /**
     * @var string
     *
     * @ORM\Column(name="controller", type="string", length=255, nullable=true)
     */
    private $controller;

    /**
     * @var string
     *
     * @ORM\Column(name="route", type="string", length=1000, nullable=true)
     */
    private $route;

    /**
     * @var string
     *
     * @ORM\Column(name="route_params", type="text", nullable=true)
     */
    private $routeParams;

    /**
     * @var string
     *
     * @ORM\Column(name="ip", type="string", length=255, nullable=true)
     */
    private $ip;

    /**
     * @var string
     *
     * @ORM\Column(name="user_name", type="string", length=500, nullable=true)
     */
    private $userName;

    /**
     * @var string
     *
     * @ORM\Column(name="portal", type="string", length=500, nullable=true)
     */
    private $portal;

    /**
     * @var string
     *
     * @ORM\Column(name="query_data", type="text", nullable=true)
     */
    private $queryData;

    /**
     * @var string
     *
     * @ORM\Column(name="data", type="text", nullable=true)
     */
    private $data;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime", nullable=false)
     */
    private $createdOn;

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set method.
     *
     * @param string $method
     *
     * @return AuditRequest
     */
    public function setMethod($method)
    {
        $this->method = $method;

        return $this;
    }

    /**
     * Get method.
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Set controller.
     *
     * @param string $controller
     *
     * @return AuditRequest
     */
    public function setController($controller)
    {
        $this->controller = $controller;

        return $this;
    }

    /**
     * Get controller.
     *
     * @return string
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * Set route.
     *
     * @param string $route
     *
     * @return AuditRequest
     */
    public function setRoute($route)
    {
        $this->route = $route;

        return $this;
    }

    /**
     * Get route.
     *
     * @return string
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * Set routeParams.
     *
     * @param string $routeParams
     *
     * @return AuditRequest
     */
    public function setRouteParams($routeParams)
    {
        $this->routeParams = $routeParams;

        return $this;
    }

    /**
     * Get routeParams.
     *
     * @return string
     */
    public function getRouteParams()
    {
        return $this->routeParams;
    }

    /**
     * Set ip.
     *
     * @param string $ip
     *
     * @return AuditRequest
     */
    public function setIp($ip)
    {
        $this->ip = $ip;

        return $this;
    }

    /**
     * Get ip.
     *
     * @return string
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * Set userName.
     *
     * @param string $userName
     *
     * @return AuditRequest
     */
    public function setUserName($userName)
    {
        $this->userName = $userName;

        return $this;
    }

    /**
     * Get userName.
     *
     * @return string
     */
    public function getUserName()
    {
        return $this->userName;
    }

    /**
     * Set portal.
     *
     * @param string $portal
     *
     * @return AuditRequest
     */
    public function setPortal($portal)
    {
        $this->portal = $portal;

        return $this;
    }

    /**
     * Get portal.
     *
     * @return string
     */
    public function getPortal()
    {
        return $this->portal;
    }

    /**
     * Set queryData.
     *
     * @param string $queryData
     *
     * @return AuditRequest
     */
    public function setQueryData($queryData)
    {
        $this->queryData = $queryData;

        return $this;
    }

    /**
     * Get queryData.
     *
     * @return string
     */
    public function getQueryData()
    {
        return $this->queryData;
    }

    /**
     * Set data.
     *
     * @param string $data
     *
     * @return AuditRequest
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Get data.
     *
     * @return string
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return mixed
     */
    public function getCreatedOn()
    {
        return $this->createdOn;
    }

    /**
     * @return AuditRequest
     */
    public function setCreatedOn($dateTime)
    {
        $this->createdOn = $dateTime;
        return $this;
    }
}
