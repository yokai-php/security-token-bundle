<?php

declare(strict_types=1);

namespace Yokai\SecurityTokenBundle;

use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;
use Symfony\Component\Console\Application;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Yokai\DependencyInjection\CompilerPass\ArgumentRegisterTaggedServicesCompilerPass;
use Yokai\SecurityTokenBundle\Manager\UserManagerInterface;

/**
 * @author Yann Eugoné <eugone.yann@gmail.com>
 */
final class YokaiSecurityTokenBundle extends Bundle
{
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }

    public function build(ContainerBuilder $container): void
    {
        $registerTokenConfiguration = new ArgumentRegisterTaggedServicesCompilerPass(
            'yokai_security_token.configuration_registry',
            'yokai_security_token.configuration',
            null,
            0,
        );
        $registerUserManager = new ArgumentRegisterTaggedServicesCompilerPass(
            'yokai_security_token.user_manager',
            'yokai_security_token.user_manager',
            UserManagerInterface::class,
            0,
        );

        $container
            ->addCompilerPass($registerTokenConfiguration)
            ->addCompilerPass($registerUserManager)
        ;

        // DoctrineBundle is optional: only needed by the "orm" storage
        if (\class_exists(DoctrineOrmMappingsPass::class)) {
            $container->addCompilerPass(
                DoctrineOrmMappingsPass::createXmlMappingDriver(
                    [\realpath(__DIR__ . '/../config/doctrine') => 'Yokai\SecurityTokenBundle\Entity'],
                    ['yokai_security_token.orm.entity_manager'],
                    'yokai_security_token.orm_storage',
                ),
            );
        }
    }

    public function registerCommands(Application $application): void
    {
        // commands are registered as services
    }
}
