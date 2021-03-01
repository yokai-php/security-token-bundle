<?php

namespace Yokai\SecurityTokenBundle;

use Symfony\Component\Console\Application;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Yokai\DependencyInjection\CompilerPass\ArgumentRegisterTaggedServicesCompilerPass;
use Yokai\SecurityTokenBundle\Manager\UserManagerInterface;

/**
 * @author Yann Eugoné <eugone.yann@gmail.com>
 */
class YokaiSecurityTokenBundle extends Bundle
{
    /**
     * @inheritDoc
     */
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }

    /**
     * @inheritDoc
     */
    public function build(ContainerBuilder $container): void
    {
        $registerTokenConfiguration = new ArgumentRegisterTaggedServicesCompilerPass(
            'yokai_security_token.configuration_registry',
            'yokai_security_token.configuration',
            null,
            0
        );
        $registerUserManager = new ArgumentRegisterTaggedServicesCompilerPass(
            'yokai_security_token.user_manager',
            'yokai_security_token.user_manager',
            UserManagerInterface::class,
            0
        );

        $container
            ->addCompilerPass($registerTokenConfiguration)
            ->addCompilerPass($registerUserManager)
        ;
    }

    /**
     * @inheritDoc
     */
    public function registerCommands(Application $application): void
    {
        // commands are registered as services
    }
}
