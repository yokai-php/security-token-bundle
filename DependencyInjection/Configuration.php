<?php

namespace Yokai\SecurityTokenBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @author Yann Eugoné <eugone.yann@gmail.com>
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

        $root->addDefaultsIfNotSet();
        $root
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
                    ->integerNode('usages')
                        ->defaultValue(1)
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

        $node->addDefaultsIfNotSet();
        $node
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
