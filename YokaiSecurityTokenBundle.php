<?php

namespace Yokai\SecurityTokenBundle;

use Symfony\Component\Console\Application;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Yokai\DependencyInjection\CompilerPass\ArgumentRegisterTaggedServicesCompilerPass;
use Yokai\SecurityTokenBundle\DependencyInjection\YokaiSecurityTokenExtension;

/**
 * @author Yann Eugoné <yann.eugone@gmail.com>
 */
class YokaiSecurityTokenBundle extends Bundle
{
    /**
     * @inheritDoc
     */
    public function build(ContainerBuilder $container)
    {
        $container
            ->addCompilerPass(
                new ArgumentRegisterTaggedServicesCompilerPass(
                    'yokai_security_token.configuration_registry',
                    'yokai_security_token.configuration',
                    null,
                    0
                )
            )
        ;
    }

    /**
     * @inheritDoc
     */
    public function getContainerExtension()
    {
        return new YokaiSecurityTokenExtension('yokai_security_token');
    }

    /**
     * @inheritDoc
     */
    public function getNamespace()
    {
        return __NAMESPACE__;
    }

    /**
     * @inheritDoc
     */
    public function getPath()
    {
        return __DIR__;
    }

    /**
     * @inheritDoc
     */
    public function registerCommands(Application $application)
    {
    }
}
