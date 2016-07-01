<?php

namespace Yokai\SecurityTokenBundle\DependencyInjection;

use ReflectionClass;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Yokai\SecurityTokenBundle\DependencyInjection\Factory\TokenConfigurationFactory;

/**
 * @author Yann Eugoné <yann.eugone@gmail.com>
 */
class YokaiSecurityTokenExtension extends Extension
{
    /**
     * @var string
     */
    private $name;

    /**
     * @param string $name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

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
     * @inheritDoc
     */
    public function getConfiguration(array $config, ContainerBuilder $container)
    {
        $configuration = new Configuration($this->name);
        $reflection = new ReflectionClass($configuration);
        $container->addResource(new FileResource($reflection->getFileName()));

        return $configuration;
    }

    /**
     * @param array            $config
     * @param ContainerBuilder $container
     */
    private function registerTokens(array $config, ContainerBuilder $container)
    {
        foreach ($config['tokens'] as $name => $token) {
            TokenConfigurationFactory::create($name, $token['generator'], $token['duration'], $container);
        }
    }

    /**
     * @param array            $config
     * @param ContainerBuilder $container
     */
    private function registerAliases(array $config, ContainerBuilder $container)
    {
        foreach ($config['services'] as $name => $service) {
            $container->setAlias(sprintf('yokai_security_token.resolved.%s', $name), $service);
        }
    }
}
