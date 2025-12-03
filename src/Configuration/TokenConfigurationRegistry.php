<?php

declare(strict_types=1);

namespace Yokai\SecurityTokenBundle\Configuration;

use BadMethodCallException;

/**
 * @author Yann Eugoné <eugone.yann@gmail.com>
 */
final class TokenConfigurationRegistry
{
    /**
     * @var array<string, TokenConfiguration>
     */
    private readonly array $configurations;

    /**
     * @param list<TokenConfiguration> $configurations
     */
    public function __construct(array $configurations)
    {
        $indexedConfigurations = [];
        foreach ($configurations as $configuration) {
            $indexedConfigurations[$configuration->purpose] = $configuration;
        }
        $this->configurations = $indexedConfigurations;
    }

    /**
     * Get configuration.
     *
     * @param string $purpose Token purpose
     *
     * @throws BadMethodCallException
     */
    public function get(string $purpose): TokenConfiguration
    {
        return $this->configurations[$purpose]
            ?? throw new BadMethodCallException(
                \sprintf('There is no configured security token on "%s" purpose.', $purpose),
            );
    }
}
