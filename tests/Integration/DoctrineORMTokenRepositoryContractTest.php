<?php

declare(strict_types=1);

namespace Yokai\SecurityTokenBundle\Tests\Integration;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Yokai\SecurityTokenBundle\Repository\TokenRepositoryInterface;
use Yokai\SecurityTokenBundle\Test\TokenRepositoryTestCase;
use Yokai\SecurityTokenBundle\Tests\Kernel;

final class DoctrineORMTokenRepositoryContractTest extends TokenRepositoryTestCase
{
    private Kernel $kernel;
    private EntityManagerInterface $manager;

    protected function tearDown(): void
    {
        if (isset($this->kernel)) {
            $this->kernel->shutdown();
            \restore_exception_handler();
        }
        parent::tearDown();
    }

    protected function createRepository(): TokenRepositoryInterface
    {
        $this->kernel = new Kernel('test', true);
        $this->kernel->boot();
        $container = $this->kernel->getContainer();
        $this->manager = $container->get('doctrine')->getManager();
        (new SchemaTool($this->manager))->createSchema($this->manager->getMetadataFactory()->getAllMetadata());

        return $container->get('yokai_security_token.token_repository');
    }

    protected function refresh(): void
    {
        $this->manager->clear();
    }
}
