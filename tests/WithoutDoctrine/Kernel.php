<?php

declare(strict_types=1);

namespace Yokai\SecurityTokenBundle\Tests\WithoutDoctrine;

use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Yokai\SecurityTokenBundle\YokaiSecurityTokenBundle;

/**
 * An application that stores tokens without Doctrine.
 */
final class Kernel extends BaseKernel
{
    public function registerBundles(): iterable
    {
        return [
            new FrameworkBundle(),
            new YokaiSecurityTokenBundle(),
        ];
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(static function (ContainerBuilder $container): void {
            $container->loadFromExtension('framework', ['test' => true, 'secret' => 'test']);
            $container->loadFromExtension('yokai_security_token', [
                'storage' => 'memory',
                'tokens' => ['reset_password' => null],
            ]);
            $container->register(UserManager::class, UserManager::class)
                ->addTag('yokai_security_token.user_manager');
        });
    }

    public function getProjectDir(): string
    {
        return __DIR__;
    }

    public function getCacheDir(): string
    {
        return \sys_get_temp_dir() . '/' . BaseKernel::VERSION . '/without-doctrine/cache/' . $this->environment;
    }

    public function getLogDir(): string
    {
        return \sys_get_temp_dir() . '/' . BaseKernel::VERSION . '/without-doctrine/logs';
    }
}
