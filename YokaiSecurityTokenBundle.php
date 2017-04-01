<?php

namespace Yokai\SecurityTokenBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Yokai\DependencyInjection\CompilerPass\ArgumentRegisterTaggedServicesCompilerPass;

/**
 * @author Yann Eugoné <eugone.yann@gmail.com>
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
}
