<?php

namespace Yokai\SecurityTokenBundle\Configuration;

use Yokai\SecurityTokenBundle\Generator\TokenGeneratorInterface;

/**
 * @author Yann Eugoné <eugone.yann@gmail.com>
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
     * @var integer
     */
    private $usages;

    /**
     * @var string
     */
    private $keep;

    /**
     * @param string                  $purpose
     * @param TokenGeneratorInterface $generator
     * @param string                  $duration
     * @param integer                 $usages
     * @param string                  $keep
     */
    public function __construct($purpose, TokenGeneratorInterface $generator, $duration, $usages, $keep)
    {
        $this->purpose = $purpose;
        $this->generator = $generator;
        $this->duration = $duration;
        $this->usages = $usages;
        $this->keep = $keep;
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

    /**
     * @return integer
     */
    public function getUsages()
    {
        return $this->usages;
    }

    /**
     * @return string
     */
    public function getKeep()
    {
        return $this->keep;
    }
}
