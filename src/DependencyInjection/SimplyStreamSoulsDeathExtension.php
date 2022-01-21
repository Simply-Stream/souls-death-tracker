<?php

namespace SimplyStream\SoulsDeathBundle\DependencyInjection;

use SimplyStream\SoulsDeathBundle\Repository\CounterRepository;
use SimplyStream\SoulsDeathBundle\Repository\GameRepository;
use SimplyStream\SoulsDeathBundle\Repository\SectionRepository;
use SimplyStream\SoulsDeathBundle\Repository\TrackerRepository;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

class SimplyStreamSoulsDeathExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.xml');

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $this->configurePersistence($config['objects'], $container);

        $container->addAliases(
            [
                TrackerRepository::class => 'soulsdeath.repository.tracker',
                SectionRepository::class => 'soulsdeath.repository.section',
                CounterRepository::class => 'soulsdeath.repository.counter',
                GameRepository::class => 'soulsdeath.repository.game',
            ]
        );
    }

    public function configurePersistence(array $objects, ContainerBuilder $container): void
    {
        foreach ($objects as $object => $services) {
            if (\array_key_exists('model', $services)) {
                $repositoryClass = $services['repository'];
                $repositoryDefinition = new Definition($repositoryClass);

                $container->setDefinition(\sprintf('soulsdeath.repository.%s', $object), $repositoryDefinition)
                    ->setPublic(true)
                    ->setLazy(true);
            }
        }
    }

    public function getAlias()
    {
        return 'simplystream_soulsdeath';
    }
}
