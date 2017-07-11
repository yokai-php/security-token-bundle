<?php

namespace Yokai\SecurityTokenBundle\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * @author Yann Eugoné <eugone.yann@gmail.com>
 */
class CreateTokenEvent extends Event
{
    /**
     * @var string
     */
    private $purpose;

    /**
     * @var mixed
     */
    private $user;

    /**
     * @var array
     */
    private $payload;

    /**
     * @param string $purpose
     * @param mixed  $user
     * @param array  $payload
     */
    public function __construct($purpose, $user, array $payload)
    {
        $this->purpose = $purpose;
        $this->user = $user;
        $this->payload = $payload;
    }

    /**
     * @return string
     */
    public function getPurpose()
    {
        return $this->purpose;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return array
     */
    public function getPayload()
    {
        return $this->payload;
    }

    /**
     * @param array $payload
     */
    public function setPayload($payload)
    {
        $this->payload = $payload;
    }

    /**
     * @param array $payload
     */
    public function addPayload($payload)
    {
        $this->payload = array_merge($this->payload, $payload);
    }
}
