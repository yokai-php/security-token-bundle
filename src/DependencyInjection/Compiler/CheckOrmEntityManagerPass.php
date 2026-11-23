<?php

declare(strict_types=1);

namespace Yokai\SecurityTokenBundle\DependencyInjection\Compiler;

use LogicException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Fails with a clear message when the entity manager configured for the "orm" storage does not exist,
 * before Doctrine mapping registration fails on a missing service.
 *
 * @author Yann Eugoné <eugone.yann@gmail.com>
 */
final class CheckOrmEntityManagerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasParameter('yokai_security_token.orm.entity_manager')) {
            return; // default entity manager
        }

        /** @var string $entityManager */
        $entityManager = $container->getParameter('yokai_security_token.orm.entity_manager');
        if ($container->has(\sprintf('doctrine.orm.%s_entity_manager', $entityManager))) {
            return;
        }

        $known = '';
        if ($container->hasParameter('doctrine.entity_managers')) {
            /** @var array<string, string> $entityManagers */
            $entityManagers = $container->getParameter('doctrine.entity_managers');
            $known = \sprintf(' Known entity managers: "%s".', \implode('", "', \array_keys($entityManagers)));
        }

        throw new LogicException(\sprintf(
            'The "%s" entity manager, configured with "yokai_security_token.orm.entity_manager", does not exist.%s',
            $entityManager,
            $known,
        ));
    }
}
