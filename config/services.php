<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Yokai\SecurityTokenBundle\Archive\DeleteArchivist;
use Yokai\SecurityTokenBundle\Command\ArchiveTokenCommand;
use Yokai\SecurityTokenBundle\Configuration\TokenConfigurationRegistry;
use Yokai\SecurityTokenBundle\EventDispatcher;
use Yokai\SecurityTokenBundle\Factory\TokenFactory;
use Yokai\SecurityTokenBundle\Generator\Bin2HexTokenGenerator;
use Yokai\SecurityTokenBundle\InformationGuesser\InformationGuesser;
use Yokai\SecurityTokenBundle\Manager\ChainUserManager;
use Yokai\SecurityTokenBundle\Manager\TokenManager;

return static function (ContainerConfigurator $container): void {
    $container->services()

        ->set('yokai_security_token.configuration_registry', TokenConfigurationRegistry::class)
            ->args([
                [], // to be replaced by compiler pass
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

        ->set('yokai_security_token.delete_archivist', DeleteArchivist::class)
            ->args([
                service('yokai_security_token.token_repository'),
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
