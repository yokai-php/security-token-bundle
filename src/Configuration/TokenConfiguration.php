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
     * @var int
     */
    private $usages;

    /**
     * @var string
     */
    private $keep;

    /**
     * @var bool
     */
    private $unique;

    /**
     * @param string                  $purpose
     * @param TokenGeneratorInterface $generator
     * @param string                  $duration
     * @param int                     $usages
     * @param string                  $keep
     * @param boolean                 $unique
     */
    public function __construct(
        string $purpose,
        TokenGeneratorInterface $generator,
        string $duration,
        int $usages,
        string $keep,
        bool $unique
    ) {
        $this->purpose = $purpose;
        $this->generator = $generator;
        $this->duration = $duration;
        $this->usages = $usages;
        $this->keep = $keep;
        $this->unique = $unique;
    }

    /**
     * @return string
     */
    public function getPurpose(): string
    {
        return $this->purpose;
    }

    /**
     * @return TokenGeneratorInterface
     */
    public function getGenerator(): TokenGeneratorInterface
    {
        return $this->generator;
    }

    /**
     * @return string
     */
    public function getDuration(): string
    {
        return $this->duration;
    }

    /**
     * @return int
     */
    public function getUsages(): int
    {
        return $this->usages;
    }

    /**
     * @return string
     */
    public function getKeep(): string
    {
        return $this->keep;
    }

    /**
     * @return bool
     */
    public function isUnique(): bool
    {
        return $this->unique;
    }
}
