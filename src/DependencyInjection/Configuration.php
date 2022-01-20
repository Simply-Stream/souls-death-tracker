<?php

namespace SimplyStream\SoulsDeathBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treebuilder = new TreeBuilder('simplystream_soulsdeath');
        $rootnode = $treebuilder->getRootNode();

        $rootnode
            ->children()
                ->arrayNode('objects')
                    ->children()
                        ->arrayNode('user')
                            ->isRequired()
                            ->children()
                                ->scalarNode('model')->end()
                                ->scalarNode('repository')->end()
                            ->end()
                        ->end()
                        ->arrayNode('tracker')
                            ->children()
                                ->scalarNode('model')->end()
                                ->scalarNode('repository')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
           ;
        return $treebuilder;
    }
}
