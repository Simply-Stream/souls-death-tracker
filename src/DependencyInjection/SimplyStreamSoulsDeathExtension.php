<?php

namespace SimplyStream\SoulsDeathBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

class SimplyStreamSoulsDeathExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.xml');
        $loader->load('controllers.xml');

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $this->configurePersistence($config['objects'], $container);
    }

    public function configurePersistence(array $objects, ContainerBuilder $container): void
    {
        foreach ($objects as $object => $services) {
            if (\array_key_exists('model', $services)) {
                $repositoryClass = $services['repository'];
                $container->setParameter(sprintf('simplystream.soulsdeath.repository.%s.class', $object), $repositoryClass);
                $container->setParameter(sprintf('simplystream.soulsdeath.model.%s.class', $object), $services['model']);
            }
        }
    }

    public function getAlias()
    {
        return 'simplystream_soulsdeath';
    }
}
