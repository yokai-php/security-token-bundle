<?php

namespace Yokai\SecurityTokenBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @author Yann Eugoné <yann.eugone@gmail.com>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @param string $name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * @inheritdoc
     */
    public function getConfigTreeBuilder()
    {
        $builder = new TreeBuilder();
        $root = $builder->root($this->name);

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
                    ->defaultValue('yokai_security_token.information_guesser')
                ->end()
                ->scalarNode('factory')
                    ->defaultValue('yokai_security_token.token_factory')
                ->end()
                ->scalarNode('repository')
                    ->defaultValue('yokai_security_token.token_repository')
                ->end()
                ->scalarNode('manager')
                    ->defaultValue('yokai_security_token.token_manager')
                ->end()
                ->scalarNode('archivist')
                    ->defaultValue('yokai_security_token.delete_archivist')
                ->end()
            ->end()
        ;

        return $node;
    }
}
