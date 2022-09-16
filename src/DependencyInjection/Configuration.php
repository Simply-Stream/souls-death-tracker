<?php

namespace SimplyStream\SoulsDeathBundle\DependencyInjection;

use SimplyStream\SoulsDeathBundle\Entity\Counter;
use SimplyStream\SoulsDeathBundle\Entity\Game;
use SimplyStream\SoulsDeathBundle\Entity\Section;
use SimplyStream\SoulsDeathBundle\Entity\Tracker;
use SimplyStream\SoulsDeathBundle\Repository\CounterRepository;
use SimplyStream\SoulsDeathBundle\Repository\GameRepository;
use SimplyStream\SoulsDeathBundle\Repository\SectionRepository;
use SimplyStream\SoulsDeathBundle\Repository\TrackerRepository;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treebuilder = new TreeBuilder('simplystream_soulsdeath');
        $rootnode = $treebuilder->getRootNode();

        $rootnode
            ->children()
                ->scalarNode('chatmessage_producer')
                    ->isRequired()
                ->end()
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
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('model')->defaultValue(Tracker::class)->end()
                                ->scalarNode('repository')->defaultValue(TrackerRepository::class)->end()
                            ->end()
                        ->end()
                        ->arrayNode('section')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('model')->defaultValue(Section::class)->end()
                                ->scalarNode('repository')->defaultValue(SectionRepository::class)->end()
                            ->end()
                        ->end()
                        ->arrayNode('counter')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('model')->defaultValue(Counter::class)->end()
                                ->scalarNode('repository')->defaultValue(CounterRepository::class)->end()
                            ->end()
                        ->end()
                        ->arrayNode('game')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('model')->defaultValue(Game::class)->end()
                                ->scalarNode('repository')->defaultValue(GameRepository::class)->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
           ;
        return $treebuilder;
    }
}
