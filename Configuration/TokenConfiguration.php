<?php

namespace Yokai\SecurityTokenBundle\Configuration;

use Yokai\SecurityTokenBundle\Generator\TokenGeneratorInterface;

/**
 * @author Yann Eugoné <yann.eugone@gmail.com>
 */
class TokenConfiguration
{
    /**
     * @var string
     */
    private $purpose;

    /**
     * @var TokenGeneratorInterface
     */
    private $generator;

    /**
     * @var string
     */
    private $duration;

    /**
     * @param string                  $purpose
     * @param TokenGeneratorInterface $generator
     * @param string                  $duration
     */
    public function __construct($purpose, TokenGeneratorInterface $generator, $duration)
    {
        $this->purpose = $purpose;
        $this->generator = $generator;
        $this->duration = $duration;
    }

    /**
     * @return string
     */
    public function getPurpose()
    {
        return $this->purpose;
    }

    /**
     * @return TokenGeneratorInterface
     */
    public function getGenerator()
    {
        return $this->generator;
    }

    /**
     * @return string
     */
    public function getDuration()
    {
        return $this->duration;
    }
}
