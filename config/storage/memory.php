<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Yokai\SecurityTokenBundle\Repository\InMemoryTokenRepository;

return static function (ContainerConfigurator $container): void {
    $container->services()
        ->set('yokai_security_token.memory_token_repository', InMemoryTokenRepository::class)
    ;
};
