<?php

namespace DataDog\AuditBundle\Command;

use DataDog\AuditBundle\Entity\Association;
use DataDog\AuditBundle\Entity\AuditLog;
use DataDog\AuditBundle\Entity\AuditRequest;
use DataDog\AuditBundle\Repository\AssociationRepository;
use DataDog\AuditBundle\Repository\AuditLogRepository;
use DataDog\AuditBundle\Repository\AuditRequestRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\DependencyInjection\ContainerInterface;

class DeleteAuditCommand extends Command
{
    /** @var EntityManagerInterface */
    private $em;

    /** @var AssociationRepository */
    private $associationRepository;

    /** @var AuditLogRepository */
    private $auditLogRepository;

    /** @var AuditRequestRepository */
    private $auditRequestRepository;
    
    public function __construct(EntityManagerInterface $em, 
    AssociationRepository $associationRepository,
    AuditLogRepository $auditLogRepository,
    AuditRequestRepository $auditRequestRepository)
    {
        $this->em = $em;
        $this->associationRepository = $associationRepository;
        $this->auditLogRepository = $auditLogRepository;
        $this->auditRequestRepository = $auditRequestRepository;
        parent::__construct();
    }
    
    protected function configure()
    {
        $this
            ->setName('nti:audit:delete')
            ->setDescription('Delete Audit in table audit_request, audit_logs and audit_associations ')
            ->addArgument("days", InputArgument::REQUIRED, "Quantity Days for delete")
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $days = $input->getArgument("days");
        if(!$days) {
            $output->writeln("<error>The Quantity Days is required.</error>");
            return;
        }

        $date = new \DateTime();
        $dateModify = new \DateTime();
        $dateModify->modify('-'.$days.' day');
        
        $date->setTime(23,59,59);
        $dateModify->setTime(0,0,0);

        // Delete
        // $connectionName = $this->container->getParameter('nti_audit.database.connection_name');
        // $em = $this->container->get('doctrine')->getManager($connectionName);

        $associations = $this->associationRepository->findAudit($dateModify, $date);
    
        if($associations){
            foreach ($associations as $association){
                $this->em->remove($association);
            }
        }

        $auditLogs = $this->auditLogRepository->findAudit($dateModify,$date);
        if($auditLogs){
            foreach ($auditLogs as $auditLog){
                $this->em->remove($auditLog);
            }
        }

        $auditRequests = $this->auditRequestRepository->findAudit($dateModify,$date);
        if($auditRequests){
           foreach ($auditRequests as $auditRequest){
                $this->em->remove($auditRequest);
           }
        }

        try {
            $this->em->flush();
            $output->writeln("<info>The Audit was deleted</info>");
        }catch(\Exception $ex){
            $output->writeln("<error>An error occurred while delete: ".$ex->getMessage()."</error>");
        }
    }
}