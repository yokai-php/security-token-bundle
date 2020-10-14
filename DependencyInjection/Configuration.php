<?php

namespace Yokai\SecurityTokenBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\HttpKernel\Kernel;

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
        if (version_compare(Kernel::VERSION, '4.2') >= 0) {
            $builder = new TreeBuilder('yokai_security_token');
            $root = $builder->getRootNode();
        } else {
            $builder = new TreeBuilder();
            $root = $builder->root('yokai_security_token');
        }

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
        if (version_compare(Kernel::VERSION, '4.2') >= 0) {
            $builder = new TreeBuilder('tokens');
            $node = $builder->getRootNode();
        } else {
            $builder = new TreeBuilder();
            $node = $builder->root('tokens');
        }

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
    private function getServicesNode()
    {
        if (version_compare(Kernel::VERSION, '4.2') >= 0) {
            $builder = new TreeBuilder('services');
            $node = $builder->getRootNode();
        } else {
            $builder = new TreeBuilder();
            $node = $builder->root('services');
        }

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
