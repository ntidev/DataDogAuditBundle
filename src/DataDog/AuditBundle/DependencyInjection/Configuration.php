<?php

namespace DataDog\AuditBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Configuration for DataDog/AuditBundle
 */
class Configuration implements ConfigurationInterface
{

    /**
     * {@inheritDoc}
     * @see \Symfony\Component\Config\Definition\ConfigurationInterface::getConfigTreeBuilder()
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('data_dog_audit');
        if (method_exists($treeBuilder, 'getRootNode')) {
            $rootNode = $treeBuilder->getRootNode();
        } else {
            $rootNode = $treeBuilder->root('data_dog_audit');
        }

        $rootNode
            ->children()
                ->arrayNode('database')
                    ->children()
                        ->scalarNode("connection_name")->end()
                ->end()
            ->end()
        ;

        $rootNode
            ->children()
                ->arrayNode('audited_entities')
                    ->canBeUnset()
                    ->performNoDeepMerging()
                    ->prototype('scalar')->end()
                ->end()
            ->end()
        ;

        $rootNode
            ->children()
                ->arrayNode('unaudited_entities')
                    ->canBeUnset()
                    ->performNoDeepMerging()
                    ->prototype('scalar')->end()
                ->end()
            ->end()
        ;

        $rootNode
            ->children()
                ->arrayNode('audit_request')
                    ->children()
                        ->scalarNode("enabled")->defaultTrue()->end()
                ->end()
            ->end()
        ;

        $rootNode->children()
            ->arrayNode('unaudited_fields')
                ->arrayPrototype()
                    ->arrayPrototype()
                        ->prototype('scalar')->end()
                    ->end()
                ->end()
            ->end()
        ;

        $rootNode
        ->children()
            ->arrayNode('unaudited_request_fields')
                ->canBeUnset()
                ->performNoDeepMerging()
                ->prototype('scalar')->end()
            ->end()
        ->end()
        ;

        return $treeBuilder;
    }

}
