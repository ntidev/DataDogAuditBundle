<?php

namespace DataDog\AuditBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * 
 * Association
 * 
 */
#[ORM\Table(name: 'audit_associations')]
#[ORM\Entity(repositoryClass: 'DataDog\AuditBundle\Repository\AssociationRepository')]
#[ORM\HasLifecycleCallbacks()]
class Association
{
    /**
     * @var bigint
     */
    #[ORM\Column(name: 'id', type: 'bigint')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private $id;

    /**
     * 
     */
    #[ORM\Column(length: 128)]
    private $typ;

    /**
     * 
     */
    #[ORM\Column(length: 128)]
    private $tbl;

    /**
     * 
     */
    #[ORM\Column(nullable: true)]
    private $label;

    /**
     * 
     */
    #[ORM\Column()]
    private $fk;

    /**
     * 
     */
    #[ORM\Column()]
    private $class;

    /**
     *  @var datetime
     */
    #[ORM\Column(type: 'datetime')]
    private $createdOn;

    /**
     * @var string
     * 
     */
    #[ORM\Column(name: 'app_name', type: 'text', nullable: true)]
    private $appName;
    
    /**
     * Get app name
     *
     * @return string
     */
    public function getAppName()
    {
        return $this->appName;
    }
    
    /**
     * Set app name
     *appName
     * @param string $app_name
     * @return Log
     */
    public function setAppName($appname)
    {
        $this->appName = $appname;
        return $this;
    }   

    public function getId()
    {
        return $this->id;
    }

    public function getTyp()
    {
        return $this->typ;
    }

    public function getTypLabel()
    {
        $words = explode('.', $this->getTyp());
        return implode(' ', array_map('ucfirst', explode('_', end($words))));
    }

    public function getTbl()
    {
        return $this->tbl;
    }

    public function getLabel()
    {
        return $this->label;
    }

    public function getFk()
    {
        return $this->fk;
    }

    public function getClass()
    {
        return $this->class;
    }

    public function getCreatedOn()
    {
        return $this->createdOn;
    }

}
