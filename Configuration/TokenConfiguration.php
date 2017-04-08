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
     * @param string                  $purpose
     * @param TokenGeneratorInterface $generator
     * @param string                  $duration
     * @param integer                 $usages
     */
    public function __construct($purpose, TokenGeneratorInterface $generator, $duration, $usages)
    {
        $this->purpose = $purpose;
        $this->generator = $generator;
        $this->duration = $duration;
        $this->usages = $usages;
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
     * @return int
     */
    public function getUsages()
    {
        return $this->usages;
    }
}
