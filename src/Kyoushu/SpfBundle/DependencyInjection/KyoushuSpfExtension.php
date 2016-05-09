<?php

namespace Kyoushu\SpfBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader;

class KyoushuSpfExtension extends Extension
{

    /**
     * @param array $configs
     * @param ContainerBuilder $container
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');

        $this->loadDefaultFragments($config, $container);
    }

    /**
     * @param array $config
     * @param ContainerBuilder $container
     */
    protected function loadDefaultFragments(array $config, ContainerBuilder $container)
    {
        $registryDefinition = $container->findDefinition('kyoushu_spf.default_fragment_registry');

        foreach($config['default_fragments'] as $fragmentConfig){

            $definition = new Definition('Kyoushu\SpfBundle\Templating\Fragment', array(
                $fragmentConfig['name'],
                $fragmentConfig['type'],
                $fragmentConfig['value']
            ));

            $registryDefinition->addMethodCall('add', array($definition));

        }
    }

}