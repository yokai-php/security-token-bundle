<?php

namespace Yokai\SecurityTokenBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Yokai\SecurityTokenBundle\Archive\DeleteArchivist;
use Yokai\SecurityTokenBundle\Factory\TokenFactory;
use Yokai\SecurityTokenBundle\InformationGuesser\InformationGuesser;
use Yokai\SecurityTokenBundle\Manager\TokenManager;
use Yokai\SecurityTokenBundle\Manager\UserManager;
use Yokai\SecurityTokenBundle\Repository\TokenRepository;

/**
 * @author Yann Eugoné <yann.eugone@gmail.com>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * @inheritdoc
     */
    public function getConfigTreeBuilder()
    {
        $builder = new TreeBuilder();
        $root = $builder->root('yokai_security_token');

        $root
            ->addDefaultsIfNotSet()
            ->children()
                ->append($this->getTokensNode())
                ->append($this->getServicesNode())
            ->end()
        ;

        return $builder;
    }

    /**
     * @return NodeDefinition
     */
    private function getTokensNode()
    {
        $builder = new TreeBuilder();
        $node = $builder->root('tokens');

        $node
            ->useAttributeAsKey('purpose')
            ->prototype('array')
                ->children()
                    ->scalarNode('generator')
                        ->defaultValue('yokai_security_token.open_ssl_token_generator')
                    ->end()
                    ->scalarNode('duration')
                        ->defaultValue('+2 days')
                    ->end()
                ->end()
            ->end()
        ;

        return $node;
    }

    /**
     * @return NodeDefinition
     */
    private function getServicesNode()
    {
        $builder = new TreeBuilder();
        $node = $builder->root('services');

        $node
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('information_guesser')
                    ->defaultValue('yokai_security_token.default_information_guesser')
                ->end()
                ->scalarNode('token_factory')
                    ->defaultValue('yokai_security_token.default_token_factory')
                ->end()
                ->scalarNode('token_repository')
                    ->defaultValue('yokai_security_token.default_token_repository')
                ->end()
                ->scalarNode('token_manager')
                    ->defaultValue('yokai_security_token.default_token_manager')
                ->end()
                ->scalarNode('user_manager')
                    ->defaultValue('yokai_security_token.default_user_manager')
                ->end()
                ->scalarNode('archivist')
                    ->defaultValue('yokai_security_token.delete_archivist')
                ->end()
            ->end()
        ;

        return $node;
    }
}
