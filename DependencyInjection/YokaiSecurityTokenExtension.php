<?php

namespace Yokai\SecurityTokenBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Yokai\SecurityTokenBundle\Archive\ArchivistInterface;
use Yokai\SecurityTokenBundle\DependencyInjection\Factory\TokenConfigurationFactory;
use Yokai\SecurityTokenBundle\Factory\TokenFactoryInterface;
use Yokai\SecurityTokenBundle\InformationGuesser\InformationGuesserInterface;
use Yokai\SecurityTokenBundle\Manager\TokenManagerInterface;
use Yokai\SecurityTokenBundle\Manager\UserManagerInterface;
use Yokai\SecurityTokenBundle\Repository\TokenRepositoryInterface;

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
        $this->registerAutoconfigureAliases($container);
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
                $token['keep'],
                $token['unique'],
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
        $aliasExists = class_exists('Symfony\Component\DependencyInjection\Alias');
        $isTest = $container->getParameter('kernel.environment') === 'test';

        foreach ($config['services'] as $name => $service) {
            $alias = $container->setAlias(sprintf('yokai_security_token.%s', $name), $service);
            if ($aliasExists && $isTest) {
                $alias->setPublic(true);
            }
        }
    }

    /**
     * @param ContainerBuilder $container
     */
    private function registerAutoconfigureAliases(ContainerBuilder $container)
    {
        $interfaceMap = [
            'information_guesser' => InformationGuesserInterface::class,
            'token_factory' => TokenFactoryInterface::class,
            'token_repository' => TokenRepositoryInterface::class,
            'token_manager' => TokenManagerInterface::class,
            'user_manager' => UserManagerInterface::class,
            'archivist' => ArchivistInterface::class,
        ];

        foreach ($interfaceMap as $service => $interface) {
            $container->setAlias($interface, sprintf('yokai_security_token.%s', $service));
        }
    }
}
