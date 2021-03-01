<?php

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

/**
 * @author Yann Eugoné <eugone.yann@gmail.com>
 */
class YokaiSecurityTokenTestKernel extends Kernel
{
    public function registerBundles(): iterable
    {
        return [
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Yokai\SecurityTokenBundle\YokaiSecurityTokenBundle(),
        ];
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(__DIR__ . '/config.yml');
    }

    public function getProjectDir(): string
    {
        return dirname(__DIR__);
    }

    public function getCacheDir(): string
    {
        return sys_get_temp_dir() . '/' . Kernel::VERSION . '/cache/' . $this->environment;
    }

    public function getLogDir(): string
    {
        return sys_get_temp_dir() . '/' . Kernel::VERSION . '/logs';
    }
}
