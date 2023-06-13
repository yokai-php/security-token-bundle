<?php

declare(strict_types=1);

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

    public function getPurpose(): string
    {
        return $this->purpose;
    }

    public function getGenerator(): TokenGeneratorInterface
    {
        return $this->generator;
    }

    public function getDuration(): string
    {
        return $this->duration;
    }

    public function getUsages(): int
    {
        return $this->usages;
    }

    public function getKeep(): string
    {
        return $this->keep;
    }

    public function isUnique(): bool
    {
        return $this->unique;
    }
}
