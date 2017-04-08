<?php

namespace Yokai\SecurityTokenBundle\Tests\DependencyInjection;

use Doctrine\Common\Persistence\Mapping\Driver\MappingDriverChain;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Prophecy\Prophecy\ProphecySubjectInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpFoundation\RequestStack;
use Yokai\SecurityTokenBundle\Archive\ArchivistInterface;
use Yokai\SecurityTokenBundle\Configuration\TokenConfiguration;
use Yokai\SecurityTokenBundle\Factory\TokenFactoryInterface;
use Yokai\SecurityTokenBundle\Generator\OpenSslTokenGenerator;
use Yokai\SecurityTokenBundle\Generator\TokenGeneratorInterface;
use Yokai\SecurityTokenBundle\InformationGuesser\InformationGuesserInterface;
use Yokai\SecurityTokenBundle\Manager\TokenManagerInterface;
use Yokai\SecurityTokenBundle\Manager\UserManagerInterface;
use Yokai\SecurityTokenBundle\Repository\TokenRepositoryInterface;
use Yokai\SecurityTokenBundle\YokaiSecurityTokenBundle;

/**
 * @author Yann Eugoné <eugone.yann@gmail.com>
 */
class DependencyInjectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ContainerBuilder
     */
    private $container;

    protected function setUp()
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
        $this->container->set('logger', $this->prophesize(LoggerInterface::class)->reveal());
        $this->container->setDefinition('doctrine.orm.default_entity_manager', new Definition(EntityManager::class));
        $this->container->setDefinition('doctrine.orm.default_metadata_driver', new Definition(MappingDriverChain::class));
        $this->container->setDefinition('doctrine.orm.default_configuration', new Definition(Configuration::class));
        $this->container->setDefinition('request_stack', new Definition(RequestStack::class));
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
            $service = $this->prophesize($class)->reveal();
            $this->container->setDefinition($id, new Definition(get_class($service)));
        }

        $this->container->registerExtension($bundle->getContainerExtension());
        $bundle->build($this->container);
    }

    /**
     * @test
     * @dataProvider configurationProvider
     */
    public function it_parse_configuration_as_expected($resource, array $tokens, array $aliases)
    {
        // for test purpose, all services are switched to public
        $this->container->addCompilerPass(new class implements CompilerPassInterface {
            public function process(ContainerBuilder $container)
            {
                $container->findDefinition('yokai_security_token.configuration_registry')->setPublic(true);
                $container->findDefinition('yokai_security_token.default_information_guesser')->setPublic(true);
                $container->findDefinition('yokai_security_token.default_token_factory')->setPublic(true);
                $container->findDefinition('yokai_security_token.default_token_repository')->setPublic(true);
                $container->findDefinition('yokai_security_token.default_token_manager')->setPublic(true);
                $container->findDefinition('yokai_security_token.default_user_manager')->setPublic(true);
                $container->findDefinition('yokai_security_token.delete_archivist')->setPublic(true);
            }
        });

        $this->loadConfiguration($resource);
        $this->container->compile();

        foreach ($tokens as $tokenId => $tokenConfig) {
            $token = $this->container->get('yokai_security_token.configuration_registry')->get($tokenId);

            self::assertInstanceOf(TokenConfiguration::class, $token);
            self::assertInstanceOf($tokenConfig['generator'], $token->getGenerator());
            self::assertSame($tokenId, $token->getPurpose());
            self::assertSame($tokenConfig['duration'], $token->getDuration());
            self::assertSame($tokenConfig['usages'], $token->getUsages());
        }

        foreach ($aliases as $alias => $expectedId) {
            self::assertTrue(
                $this->container->hasAlias($alias),
                "An alias named \"$alias\" exists."
            );
            self::assertSame(
                $expectedId, (string) $this->container->getAlias($alias),
                "The alias named \"$alias\" refer to \"$expectedId\"."
            );
        }
    }

    /**
     * @param string $resource
     */
    protected function loadConfiguration($resource)
    {
        $locator = new FileLocator(__DIR__.'/configuration/');
        $path = $locator->locate($resource);

        switch (pathinfo($path, PATHINFO_EXTENSION)) {
            case 'yml':
                $loader = new Loader\YamlFileLoader($this->container, $locator);
                break;

            //todo nice to have : support more configuration format

            default:
                throw new \InvalidArgumentException('File ' . $path . ' is not supported.');
                break;
        }

        $loader->load($resource);
    }

    public function configurationProvider()
    {
        $defaultAliases = [
            'yokai_security_token.information_guesser' => 'yokai_security_token.default_information_guesser',
            'yokai_security_token.token_factory' => 'yokai_security_token.default_token_factory',
            'yokai_security_token.token_repository' => 'yokai_security_token.default_token_repository',
            'yokai_security_token.token_manager' => 'yokai_security_token.default_token_manager',
            'yokai_security_token.user_manager' => 'yokai_security_token.default_user_manager',
            'yokai_security_token.archivist' => 'yokai_security_token.delete_archivist',
        ];

        foreach ($this->formatProvider() as $format) {
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
                        'generator' => OpenSslTokenGenerator::class,
                        'duration' => '+2 days',
                        'usages' => 1,
                    ],
                    'security_password_reset' => [
                        'generator' => OpenSslTokenGenerator::class,
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
                        'generator' => ProphecySubjectInterface::class,
                        'duration' => '+1 month',
                        'usages' => 2,
                    ],
                    'security_password_reset' => [
                        'generator' => ProphecySubjectInterface::class,
                        'duration' => '+2 monthes',
                        'usages' => 3,
                    ],
                ],
                [
                    'yokai_security_token.information_guesser' => 'information_guesser_mock',
                    'yokai_security_token.token_factory' => 'token_factory_mock',
                    'yokai_security_token.token_repository' => 'token_repository_mock',
                    'yokai_security_token.token_manager' => 'token_manager_mock',
                    'yokai_security_token.user_manager' => 'user_manager_mock',
                    'yokai_security_token.archivist' => 'archivist_mock',
                ],
            ];
        }
    }

    public function formatProvider()
    {
        yield ['yml'];
    }
}
