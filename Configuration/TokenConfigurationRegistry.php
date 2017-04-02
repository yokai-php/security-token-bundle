<?php

namespace Yokai\SecurityTokenBundle\Configuration;

use BadMethodCallException;

/**
 * @author Yann Eugoné <eugone.yann@gmail.com>
 */
class TokenConfigurationRegistry
{
    /**
     * @var TokenConfiguration[]
     */
    private $configurations;

    /**
     * @param TokenConfiguration[] $configurations
     */
    public function __construct(array $configurations)
    {
        $this->configurations = [];
        foreach ($configurations as $configuration) {
            $this->configurations[$configuration->getPurpose()] = $configuration;
        }
    }

    /**
     * @param string $purpose
     *
     * @return TokenConfiguration
     */
    public function get($purpose)
    {
        if (!isset($this->configurations[$purpose])) {
            throw new BadMethodCallException(
                sprintf('There is no configured security token on "%s" purpose.', $purpose)
            );
        }

        return $this->configurations[$purpose];
    }
}
