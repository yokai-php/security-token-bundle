<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Doctrine\ORM\EntityRepository;
use Yokai\SecurityTokenBundle\Archive\DeleteArchivist;
use Yokai\SecurityTokenBundle\Command\ArchiveTokenCommand;
use Yokai\SecurityTokenBundle\Configuration\TokenConfigurationRegistry;
use Yokai\SecurityTokenBundle\Entity\Token;
use Yokai\SecurityTokenBundle\EventDispatcher;
use Yokai\SecurityTokenBundle\Factory\TokenFactory;
use Yokai\SecurityTokenBundle\Generator\Bin2HexTokenGenerator;
use Yokai\SecurityTokenBundle\InformationGuesser\InformationGuesser;
use Yokai\SecurityTokenBundle\Manager\ChainUserManager;
use Yokai\SecurityTokenBundle\Manager\DoctrineUserManager;
use Yokai\SecurityTokenBundle\Manager\TokenManager;
use Yokai\SecurityTokenBundle\Repository\DoctrineORMTokenRepository;

return static function (ContainerConfigurator $container): void {
    $container->services()

        ->set('yokai_security_token.token_entity_repository', EntityRepository::class)
            ->factory([service('doctrine.orm.default_entity_manager'), 'getRepository'])
            ->args([
                Token::class,
            ])

        ->set('yokai_security_token.configuration_registry', TokenConfigurationRegistry::class)
            ->args([
                [], // to be replaced by compiler pass
            ])

        ->set('yokai_security_token.default_token_repository', DoctrineORMTokenRepository::class)
            ->args([
                service('doctrine.orm.default_entity_manager'),
                service('yokai_security_token.token_entity_repository'),
            ])

        ->set('yokai_security_token.bin2hex_token_generator', Bin2HexTokenGenerator::class)

        ->set('yokai_security_token.default_information_guesser', InformationGuesser::class)
            ->args([
                service('request_stack'),
            ])

        ->set('yokai_security_token.default_token_factory', TokenFactory::class)
            ->args([
                service('yokai_security_token.configuration_registry'),
                service('yokai_security_token.information_guesser'),
                service('yokai_security_token.user_manager'),
                service('yokai_security_token.token_repository'),
            ])

        ->set('yokai_security_token.default_token_manager', TokenManager::class)
            ->args([
                service('yokai_security_token.token_factory'),
                service('yokai_security_token.token_repository'),
                service('yokai_security_token.information_guesser'),
                service('yokai_security_token.user_manager'),
                service('yokai_security_token.event_dispatcher'),
            ])

        ->set('yokai_security_token.user_manager', ChainUserManager::class)
            ->args([
                [], // to be replaced by compiler pass
            ])

        ->set('yokai_security_token.doctrine_user_manager', DoctrineUserManager::class)
            ->args([
                service('doctrine'),
            ])
            ->tag('yokai_security_token.user_manager')

        ->set('yokai_security_token.delete_archivist', DeleteArchivist::class)
            ->args([
                service('yokai_security_token.token_entity_repository'),
            ])

        ->set('yokai_security_token.event_dispatcher', EventDispatcher::class)
            ->args([
                service('event_dispatcher'),
            ])

        ->set('yokai_security_token.command.archive', ArchiveTokenCommand::class)
            ->public()
            ->args([
                service('yokai_security_token.archivist'),
            ])
            ->tag('console.command')
    ;
};
