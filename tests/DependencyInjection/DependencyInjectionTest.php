<?php

declare(strict_types=1);

namespace Yokai\SecurityTokenBundle\Tests\DependencyInjection;

use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\Mapping\Driver\MappingDriverChain;
use Generator;
use LogicException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\RequestStack;
use Yokai\SecurityTokenBundle\Archive\ArchivistInterface;
use Yokai\SecurityTokenBundle\Configuration\TokenConfiguration;
use Yokai\SecurityTokenBundle\Factory\TokenFactoryInterface;
use Yokai\SecurityTokenBundle\Generator\TokenGeneratorInterface;
use Yokai\SecurityTokenBundle\InformationGuesser\InformationGuesserInterface;
use Yokai\SecurityTokenBundle\Manager\TokenManagerInterface;
use Yokai\SecurityTokenBundle\Manager\UserManagerInterface;
use Yokai\SecurityTokenBundle\Repository\TokenRepositoryInterface;
use Yokai\SecurityTokenBundle\YokaiSecurityTokenBundle;

/**
 * @author Yann Eugoné <eugone.yann@gmail.com>
 *
 * phpcs:ignoreFile PSR1.Methods.CamelCapsMethodName.NotCamelCaps
 */
final class DependencyInjectionTest extends TestCase
{
    /**
     * @var ContainerBuilder
     */
    private $container;

    /**
     * @var array<string, class-string>
     */
    private array $mockClasses = [];

    protected function setUp(): void
    {
        $bundle = new YokaiSecurityTokenBundle();
        $this->container = new ContainerBuilder();

        $bundles = [
            'FrameworkBundle' => 'Symfony\Bundle\FrameworkBundle\FrameworkBundle',
            'DoctrineBundle' => 'Doctrine\Bundle\DoctrineBundle\DoctrineBundle',
            'YokaiSecurityTokenBundle' => 'Yokai\SecurityTokenBundle\YokaiSecurityTokenBundle',
            'AppBundle' => 'AppBundle\AppBundle',
        ];

        $this->container->setParameter('kernel.debug', true);
        $this->container->setParameter('kernel.bundles', $bundles);
        $this->container->setParameter('kernel.environment', 'test');
        $this->container->set('logger', $this->createMock(LoggerInterface::class));
        $this->container->setDefinition('doctrine', new Definition(ManagerRegistry::class));
        $this->container->setDefinition('doctrine.orm.default_entity_manager', new Definition(EntityManager::class));
        $this->container->setDefinition(
            'doctrine.orm.default_metadata_driver',
            (new Definition(MappingDriverChain::class))->setPublic(true),
        );
        $this->container->setDefinition('doctrine.orm.default_configuration', new Definition(Configuration::class));
        $this->container->setDefinition('request_stack', new Definition(RequestStack::class));
        $this->container->setDefinition('event_dispatcher', new Definition(EventDispatcher::class));
        $this->container->setParameter('doctrine.default_entity_manager', 'default');

        $mocks = [
            'generator_mock' => TokenGeneratorInterface::class,
            'information_guesser_mock' => InformationGuesserInterface::class,
            'token_factory_mock' => TokenFactoryInterface::class,
            'token_repository_mock' => TokenRepositoryInterface::class,
            'token_manager_mock' => TokenManagerInterface::class,
            'user_manager_mock' => UserManagerInterface::class,
            'archivist_mock' => ArchivistInterface::class,
        ];
        foreach ($mocks as $id => $class) {
            $service = $this->createMock($class);
            $this->mockClasses[$id] = \get_class($service);
            $this->container->setDefinition($id, new Definition($this->mockClasses[$id]));
        }

        $this->container->registerExtension($bundle->getContainerExtension());
        $bundle->build($this->container);
    }

    #[DataProvider('configurationProvider')]
    public function test_it_parse_configuration_as_expected(string $resource, array $tokens, array $aliases): void
    {
        // for test purpose, all services are switched to public
        $this->container->addCompilerPass(new class implements CompilerPassInterface {
            public function process(ContainerBuilder $container): void
            {
                $container->findDefinition('yokai_security_token.configuration_registry')->setPublic(true);
                $container->findDefinition('yokai_security_token.default_information_guesser')->setPublic(true);
                $container->findDefinition('yokai_security_token.default_token_factory')->setPublic(true);
                if ($container->hasDefinition('yokai_security_token.orm_token_repository')) {
                    $container->findDefinition('yokai_security_token.orm_token_repository')->setPublic(true);
                }
                $container->findDefinition('yokai_security_token.default_token_manager')->setPublic(true);
                $container->findDefinition('yokai_security_token.user_manager')->setPublic(true);
                $container->findDefinition('yokai_security_token.delete_archivist')->setPublic(true);
            }
        });

        $this->loadConfiguration($resource);
        $this->container->compile();

        foreach ($tokens as $tokenId => $tokenConfig) {
            $token = $this->container->get('yokai_security_token.configuration_registry')->get($tokenId);

            self::assertInstanceOf(TokenConfiguration::class, $token);
            self::assertInstanceOf($tokenConfig['generator'], $token->generator);
            self::assertSame($tokenId, $token->purpose);
            self::assertSame($tokenConfig['duration'], $token->duration);
            self::assertSame($tokenConfig['usages'], $token->usages);
        }

        foreach ($aliases as $alias => $expectedId) {
            self::assertTrue(
                $this->container->has($alias),
                "An alias named \"$alias\" exists.",
            );
        }
    }

    public function test_storage_can_be_a_custom_token_repository_service(): void
    {
        $this->container->loadFromExtension('yokai_security_token', ['storage' => 'token_repository_mock']);
        $this->container->compile();

        self::assertSame(
            $this->mockClasses['token_repository_mock'],
            $this->container->findDefinition('yokai_security_token.token_repository')->getClass(),
        );
    }

    public function test_orm_storage_is_not_registered_with_a_custom_token_repository(): void
    {
        $this->container->loadFromExtension('yokai_security_token', ['storage' => 'token_repository_mock']);
        $this->container->compile();

        // private services unused at compile time are removed: look into removed ids as well
        $registered = [...$this->container->getServiceIds(), ...\array_keys($this->container->getRemovedIds())];
        self::assertNotContains('yokai_security_token.orm_token_repository', $registered);
        self::assertNotContains('yokai_security_token.token_entity_repository', $registered);
        self::assertSame([], $this->registeredMappingNamespaces());
    }

    public function test_orm_storage_registers_token_entity_mapping(): void
    {
        $this->container->loadFromExtension('yokai_security_token', []);
        $this->container->compile();

        self::assertSame(['Yokai\SecurityTokenBundle\Entity'], $this->registeredMappingNamespaces());
    }

    public function test_orm_storage_requires_doctrine_bundle(): void
    {
        $bundles = $this->container->getParameter('kernel.bundles');
        unset($bundles['DoctrineBundle']);
        $this->container->setParameter('kernel.bundles', $bundles);

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('The "orm" token storage requires DoctrineBundle');

        $this->container->loadFromExtension('yokai_security_token', []);
        $this->container->compile();
    }

    public function test_orm_storage_follows_the_default_entity_manager_whatever_its_name(): void
    {
        $this->container->setParameter('doctrine.default_entity_manager', 'main');
        $this->container->setDefinition(
            'doctrine.orm.main_metadata_driver',
            (new Definition(MappingDriverChain::class))->setPublic(true),
        );

        $this->container->loadFromExtension('yokai_security_token', []);
        $this->container->compile();

        self::assertSame(['Yokai\\SecurityTokenBundle\\Entity'], $this->registeredMappingNamespaces('main'));
    }

    public function test_orm_storage_tells_when_the_entity_manager_does_not_exist(): void
    {
        $this->container->setParameter(
            'doctrine.entity_managers',
            ['default' => 'doctrine.orm.default_entity_manager'],
        );

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage(
            'The "tokens" entity manager, configured with "yokai_security_token.orm.entity_manager", does not exist. '
            . 'Known entity managers: "default".',
        );

        $this->container->loadFromExtension('yokai_security_token', ['orm' => ['entity_manager' => 'tokens']]);
        $this->container->compile();
    }

    public function test_orm_storage_can_use_another_entity_manager(): void
    {
        // public, so that the compiler does not inline them
        $this->container->setDefinition(
            'doctrine.orm.tokens_entity_manager',
            (new Definition(EntityManager::class))->setPublic(true),
        );
        $this->container->setDefinition(
            'doctrine.orm.tokens_metadata_driver',
            (new Definition(MappingDriverChain::class))->setPublic(true),
        );

        $this->container->loadFromExtension('yokai_security_token', ['orm' => ['entity_manager' => 'tokens']]);
        $this->container->compile();

        self::assertSame(
            'doctrine.orm.tokens_entity_manager',
            (string)$this->container->findDefinition('yokai_security_token.token_repository')->getArgument(0),
        );
        self::assertSame(['Yokai\SecurityTokenBundle\Entity'], $this->registeredMappingNamespaces('tokens'));
        self::assertSame([], $this->registeredMappingNamespaces('default'));
    }

    /**
     * @return list<string>
     */
    private function registeredMappingNamespaces(string $entityManager = 'default'): array
    {
        $calls = $this->container
            ->getDefinition(\sprintf('doctrine.orm.%s_metadata_driver', $entityManager))
            ->getMethodCalls();

        return \array_values(\array_map(static fn(array $call): string => $call[1][1], $calls));
    }

    protected function loadConfiguration(string $resource): void
    {
        $locator = new FileLocator(__DIR__ . '/configuration/');
        $path = $locator->locate($resource);

        switch (\pathinfo($path, PATHINFO_EXTENSION)) {
            case 'yml':
                $loader = new Loader\YamlFileLoader($this->container, $locator);
                break;

                // todo nice to have : support more configuration format

            default:
                throw new \InvalidArgumentException('File ' . $path . ' is not supported.');
        }

        $loader->load($resource);
    }

    public static function configurationProvider(): Generator
    {
        $defaultAliases = [
            'yokai_security_token.information_guesser' => 'yokai_security_token.default_information_guesser',
            'yokai_security_token.token_factory' => 'yokai_security_token.default_token_factory',
            'yokai_security_token.token_repository' => 'yokai_security_token.orm_token_repository',
            'yokai_security_token.token_manager' => 'yokai_security_token.default_token_manager',
            'yokai_security_token.archivist' => 'yokai_security_token.delete_archivist',
        ];

        foreach (self::formatProvider() as $format) {
            $format = $format[0];

            yield $format . ' - none' => [
                'none.' . $format,
                [],
                $defaultAliases,
            ];

            yield $format . ' - names only' => [
                'names.' . $format,
                [
                    'security_password_init' => [
                        'generator' => TokenGeneratorInterface::class,
                        'duration' => '+2 days',
                        'usages' => 1,
                    ],
                    'security_password_reset' => [
                        'generator' => TokenGeneratorInterface::class,
                        'duration' => '+2 days',
                        'usages' => 1,
                    ],
                ],
                $defaultAliases,
            ];

            yield $format . ' - fully configured' => [
                'full.' . $format,
                [
                    'security_password_init' => [
                        'generator' => TokenGeneratorInterface::class,
                        'duration' => '+1 month',
                        'usages' => 2,
                    ],
                    'security_password_reset' => [
                        'generator' => TokenGeneratorInterface::class,
                        'duration' => '+2 monthes',
                        'usages' => 3,
                    ],
                ],
                [
                    'yokai_security_token.information_guesser' => 'information_guesser_mock',
                    'yokai_security_token.token_factory' => 'token_factory_mock',
                    'yokai_security_token.token_repository' => 'token_repository_mock',
                    'yokai_security_token.token_manager' => 'token_manager_mock',
                    'yokai_security_token.archivist' => 'archivist_mock',
                ],
            ];
        }
    }

    public static function formatProvider(): Generator
    {
        yield ['yml'];
    }
}
