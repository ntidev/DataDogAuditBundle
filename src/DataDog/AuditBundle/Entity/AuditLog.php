<?php

namespace DataDog\AuditBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * 
 * AuditLog
 * 
 */
#[ORM\Table(name: 'audit_logs')]
#[ORM\Entity(repositoryClass: 'DataDog\AuditBundle\Repository\AuditLogRepository')]
#[ORM\HasLifecycleCallbacks()]
class AuditLog
{
    /**
     * 
     * @var bigint
     * 
     */
    #[ORM\Column(name: 'id', type: 'bigint')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private $id;

    /**
     * 
     */
    #[ORM\Column(length: 12)]
    private $action;

    /**
     * 
     */
    #[ORM\Column(length: 12)]
    private $tbl;

    /**
     * 
     * 
     */
    #[ORM\OneToOne(targetEntity: Association::class)]
    #[ORM\JoinColumn(nullable:false)]
    private $source;

    /**
     * 
     */
    #[ORM\OneToOne(targetEntity: Association::class)]
    private $target;

    /**
     * 
     */
    #[ORM\OneToOne(targetEntity: Association::class)]
    private $blame;

    /**
     * 
     */
    #[ORM\Column(type:'json_array', nullable:true)]
    private $diff;

    /**
     * @var datetime
     */
    #[ORM\Column(type:'datetime')]
    private $loggedAt;

    /**
     * @var string
     * 
     */
    #[ORM\Column(name:'app_name', type:'text', nullable:true)]
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

    public function getAction()
    {
        return $this->action;
    }

    public function getTbl()
    {
        return $this->tbl;
    }

    public function getSource()
    {
        return $this->source;
    }

    public function getTarget()
    {
        return $this->target;
    }

    public function getBlame()
    {
        return $this->blame;
    }

    public function getDiff()
    {
        return $this->diff;
    }

    public function getLoggedAt()
    {
        return $this->loggedAt;
    }
    
}
