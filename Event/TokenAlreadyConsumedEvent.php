<?php

namespace Yokai\SecurityTokenBundle\Event;

/**
 * Event being dispatched when a Token is fetched but already consumed.
 *
 * @author Yann Eugoné <eugone.yann@gmail.com>
 */
class TokenAlreadyConsumedEvent extends TokenUsedEvent
{
}
