<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Doctrine\ORM\EntityRepository;
use Yokai\SecurityTokenBundle\Entity\Token;
use Yokai\SecurityTokenBundle\Repository\DoctrineORMTokenRepository;

return static function (ContainerConfigurator $container): void {
    $container->services()

        ->set('yokai_security_token.token_entity_repository', EntityRepository::class)
            ->factory([service('yokai_security_token.orm.entity_manager'), 'getRepository'])
            ->args([
                Token::class,
            ])

        ->set('yokai_security_token.orm_token_repository', DoctrineORMTokenRepository::class)
            ->args([
                service('yokai_security_token.orm.entity_manager'),
                service('yokai_security_token.token_entity_repository'),
            ])
    ;
};
