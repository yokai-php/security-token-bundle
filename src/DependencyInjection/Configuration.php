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
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $builder = new TreeBuilder('yokai_security_token');
        $root = $builder->getRootNode();

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
    private function getTokensNode(): NodeDefinition
    {
        $builder = new TreeBuilder('tokens');
        $node = $builder->getRootNode();

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
                    ->scalarNode('keep')
                        ->defaultValue('+1 month')
                    ->end()
                    ->booleanNode('unique')
                        ->defaultValue(false)
                    ->end()
                ->end()
            ->end()
        ;

        return $node;
    }

    /**
     * @return NodeDefinition
     */
    private function getServicesNode(): NodeDefinition
    {
        $builder = new TreeBuilder('services');
        $node = $builder->getRootNode();

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
                ->scalarNode('archivist')
                    ->defaultValue('yokai_security_token.delete_archivist')
                ->end()
            ->end()
        ;

        return $node;
    }
}
