<?php

namespace DataDog\AuditBundle\Command;

use DataDog\AuditBundle\Entity\Association;
use DataDog\AuditBundle\Entity\AuditLog;
use DataDog\AuditBundle\Entity\AuditRequest;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class DeleteAuditCommand extends ContainerAwareCommand
{
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
        $em = $this->getContainer()->get('doctrine')->getManager();

        $associations = $em->getRepository(Association::class)->findAudit($dateModify, $date);
    
        if($associations){
            foreach ($associations as $association){
                $em->remove($association);
            }
        }

        $auditLogs = $em->getRepository(AuditLog::class)->findAudit($dateModify,$date);
        if($auditLogs){
            foreach ($auditLogs as $auditLog){
                $em->remove($auditLog);
            }
        }

        $auditRequests = $em->getRepository(AuditRequest::class)->findAudit($dateModify,$date);
        if($auditRequests){
           foreach ($auditRequests as $auditRequest){
                $em->remove($auditRequest);
           }
        }

        try {
            $em->flush();
            $output->writeln("<info>The Audit was deleted</info>");
        }catch(\Exception $ex){
            $output->writeln("<error>An error occurred while delete: ".$ex->getMessage()."</error>");
        }
    }
}