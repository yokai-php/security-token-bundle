<?php

namespace Yokai\SecurityTokenBundle\DependencyInjection\Factory;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Yokai\SecurityTokenBundle\Configuration\TokenConfiguration;

/**
 * @author Yann Eugoné <yann.eugone@gmail.com>
 */
class TokenConfigurationFactory
{
    /**
     * @param string           $purpose
     * @param string           $generator
     * @param integer          $duration
     * @param ContainerBuilder $container
     */
    public static function create($purpose, $generator, $duration, ContainerBuilder $container)
    {
        $id = sprintf('yokai_security_token.configuration.%s', $purpose);

        if ($container->hasDefinition($id)) {
            throw new \RuntimeException();//todo
        }

        $definition = new Definition(
            TokenConfiguration::class,
            [
                $purpose,
                new Reference($generator),
                $duration
            ]
        );

        $definition->addTag('yokai_security_token.configuration');

        $container->setDefinition($id, $definition);
    }
}
