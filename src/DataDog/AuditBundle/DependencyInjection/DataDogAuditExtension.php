<?php

namespace DataDog\AuditBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

class DataDogAuditExtension extends Extension
{
    private $defaultConfiguration = array(
        'audit_request' => ['enabled' => false],
        'database' => array('connection_name' => 'default'),
    );

    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $auditSubscriber = $container->getDefinition('datadog.event_subscriber.audit');

        if (isset($config['audited_entities']) && !empty($config['audited_entities']))
            $auditSubscriber->addMethodCall('addAuditedEntities', array($config['audited_entities']));
        else if (isset($config['unaudited_entities']))
            $auditSubscriber->addMethodCall('addUnauditedEntities', array($config['unaudited_entities']));

        if(isset($config['audit_request']) && !empty($config['audit_request']))
            $this->defaultConfiguration['audit_request']['enabled'] = $config['audit_request']['enabled'];

        if (isset($config['unaudited_fields']) && !empty($config['unaudited_fields']))
            $auditSubscriber->addMethodCall('addUnauditedFields', array($config['unaudited_fields']));

        if (isset($config['unaudited_request_fields']) && !empty($config['unaudited_request_fields'])) {
            $this->defaultConfiguration['unaudited_request_fields'] = $config['unaudited_request_fields'];
            $container->setParameter('nti_audit.audit_request.unaudited_request_fields', $this->defaultConfiguration['unaudited_request_fields']);
        } 
        else
            $container->setParameter('nti_audit.audit_request.unaudited_request_fields', array());

        if(isset($config['database']) && isset($config['database']['connection_name']))
            $this->defaultConfiguration['database']['connection_name'] = $config['database']['connection_name'];

        $container->setParameter('nti_audit.audit_request.enabled', $this->defaultConfiguration['audit_request']['enabled']);
        $container->setParameter('nti_audit.database.connection_name', $this->defaultConfiguration['database']['connection_name']);
    }
}
