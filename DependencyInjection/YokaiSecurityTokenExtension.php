<?php

namespace Yokai\SecurityTokenBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Yokai\SecurityTokenBundle\DependencyInjection\Factory\TokenConfigurationFactory;

/**
 * @author Yann Eugoné <eugone.yann@gmail.com>
 */
class YokaiSecurityTokenExtension extends Extension
{
    /**
     * @inheritdoc
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $config = $this->processConfiguration($this->getConfiguration($configs, $container), $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        $this->registerTokens($config, $container);
        $this->registerAliases($config, $container);
    }

    /**
     * @param array            $config
     * @param ContainerBuilder $container
     */
    private function registerTokens(array $config, ContainerBuilder $container)
    {
        foreach ($config['tokens'] as $name => $token) {
            TokenConfigurationFactory::create(
                $name,
                $token['generator'],
                $token['duration'],
                $token['usages'],
                $container
            );
        }
    }

    /**
     * @param array            $config
     * @param ContainerBuilder $container
     */
    private function registerAliases(array $config, ContainerBuilder $container)
    {
        foreach ($config['services'] as $name => $service) {
            $container->setAlias(sprintf('yokai_security_token.%s', $name), $service);
        }
    }
}
