<?php

namespace Yokai\SecurityTokenBundle\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * Event being dispatched before a Token is created.
 *
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
     * @param string $purpose The token purpose
     * @param mixed  $user    The associated user
     * @param array  $payload The token payload
     */
    public function __construct($purpose, $user, array $payload)
    {
        $this->purpose = $purpose;
        $this->user = $user;
        $this->payload = $payload;
    }

    /**
     * The token purpose
     *
     * @return string
     */
    public function getPurpose()
    {
        return $this->purpose;
    }

    /**
     * The associated user
     *
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * The token payload
     *
     * @return array
     */
    public function getPayload()
    {
        return $this->payload;
    }

    /**
     * Replace token payload
     *
     * @param array $payload The new payload value
     */
    public function setPayload($payload)
    {
        $this->payload = $payload;
    }

    /**
     * Add payload information to token
     *
     * @param array $payload Some payload to add
     */
    public function addPayload($payload)
    {
        $this->payload = array_merge($this->payload, $payload);
    }
}
