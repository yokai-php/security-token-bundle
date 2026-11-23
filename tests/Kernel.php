<?php

declare(strict_types=1);

namespace Yokai\SecurityTokenBundle\Tests;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

/**
 * @author Yann Eugoné <eugone.yann@gmail.com>
 */
final class Kernel extends BaseKernel
{
    public function registerBundles(): iterable
    {
        return [
            new \Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new \Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new \Yokai\SecurityTokenBundle\YokaiSecurityTokenBundle(),
        ];
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        // an environment may have its own configuration, replacing the default one
        $config = __DIR__ . '/app/config_' . $this->environment . '.yml';
        $loader->load(\is_file($config) ? $config : __DIR__ . '/app/config.yml');
    }

    public function getProjectDir(): string
    {
        return __DIR__;
    }

    public function getCacheDir(): string
    {
        return \sys_get_temp_dir() . '/' . BaseKernel::VERSION . '/cache/' . $this->environment;
    }

    public function getLogDir(): string
    {
        return \sys_get_temp_dir() . '/' . BaseKernel::VERSION . '/logs';
    }
}
