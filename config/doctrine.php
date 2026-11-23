<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Yokai\SecurityTokenBundle\Manager\DoctrineUserManager;

return static function (ContainerConfigurator $container): void {
    $container->services()

        ->set('yokai_security_token.doctrine_user_manager', DoctrineUserManager::class)
            ->args([
                service('doctrine'),
            ])
            ->tag('yokai_security_token.user_manager')
    ;
};
