<?php

namespace Yokai\SecurityTokenBundle;

/**
 * @author Yann Eugoné <eugone.yann@gmail.com>
 */
final class TokenEvents
{
    /**
     * The CREATE_TOKEN event occurs before a token is created.
     *
     * This event allows you to determine token payload.
     * The event listener method receives a Yokai\SecurityTokenBundle\Event\CreateTokenEvent instance.
     *
     * @Event
     *
     * @var string
     */
    const CREATE_TOKEN = 'yokai_security_yoken.create_token';

    /**
     * The TOKEN_CREATED event occurs after a token is created.
     *
     * This event allows you to do be notified of successful token creations.
     * The event listener method receives a Yokai\SecurityTokenBundle\Event\TokenCreatedEvent instance.
     *
     * @Event
     *
     * @var string
     */
    const TOKEN_CREATED = 'yokai_security_yoken.token_created';

    /**
     * The CONSUME_TOKEN event occurs before a token is consumed.
     *
     * This event allows you determine token usage information.
     * The event listener method receives a Yokai\SecurityTokenBundle\Event\ConsumeTokenEvent instance.
     *
     * @Event
     *
     * @var string
     */
    const CONSUME_TOKEN = 'yokai_security_yoken.consume_token';

    /**
     * The TOKEN_CONSUMED event occurs after a token is consumed.
     *
     * This event allows you to do be notified of successful token consumptions.
     * The event listener method receives a Yokai\SecurityTokenBundle\Event\TokenConsumedEvent instance.
     *
     * @Event
     *
     * @var string
     */
    const TOKEN_CONSUMED = 'yokai_security_yoken.token_consumed';

    /**
     * The TOKEN_TOTALLY_CONSUMED event occurs after a token is totally consumed (no more usages allowed on this token).
     *
     * This event allows you to do be notified of successful token consumptions.
     * The event listener method receives a Yokai\SecurityTokenBundle\Event\TokenTotallyConsumed instance.
     *
     * @Event
     *
     * @var string
     */
    const TOKEN_TOTALLY_CONSUMED = 'yokai_security_yoken.token_used';

    /**
     * The TOKEN_RETRIEVED event occurs whenever the Yokai\SecurityTokenBundle\Manager\TokenManagerInterface
     * is returning a token.
     *
     * This event allows you to do be notified of token request that succeed.
     * The event listener method receives a Yokai\SecurityTokenBundle\Event\TokenRetrievedEvent instance.
     *
     * @Event
     *
     * @var string
     */
    const TOKEN_RETRIEVED = 'yokai_security_yoken.token_retrieved';

    /**
     * The TOKEN_NOT_FOUND event occurs whenever the Yokai\SecurityTokenBundle\Manager\TokenManagerInterface
     * throw a Yokai\SecurityTokenBundle\Exception\TokenNotFoundException.
     *
     * This event allows you to do be notified of token request that fail to not found.
     * The event listener method receives a Yokai\SecurityTokenBundle\Event\TokenNotFoundEvent instance.
     *
     * @Event
     *
     * @var string
     */
    const TOKEN_NOT_FOUND = 'yokai_security_yoken.token_not_found';

    /**
     * The TOKEN_EXPIRED event occurs whenever the Yokai\SecurityTokenBundle\Manager\TokenManagerInterface
     * throw a Yokai\SecurityTokenBundle\Exception\TokenExpiredException.
     *
     * This event allows you to do be notified of token request that fail to expired.
     * The event listener method receives a Yokai\SecurityTokenBundle\Event\TokenExpiredEvent instance.
     *
     * @Event
     *
     * @var string
     */
    const TOKEN_EXPIRED = 'yokai_security_yoken.token_expired';

    /**
     * @deprecated since 2.3 to be removed in 3.0. Use TOKEN_ALREADY_CONSUMED instead.
     *
     * The TOKEN_USED event occurs whenever the Yokai\SecurityTokenBundle\Manager\TokenManagerInterface
     * throw a Yokai\SecurityTokenBundle\Exception\TokenUsedException.
     *
     * This event allows you to do be notified of token request that fail to used.
     * The event listener method receives a Yokai\SecurityTokenBundle\Event\TokenUsedEvent instance.
     *
     * @Event
     *
     * @var string
     */
    const TOKEN_USED = 'yokai_security_yoken.token_used';

    /**
     * The TOKEN_ALREADY_CONSUMED event occurs whenever the Yokai\SecurityTokenBundle\Manager\TokenManagerInterface
     * throw a Yokai\SecurityTokenBundle\Exception\TokenConsumedException.
     *
     * This event allows you to do be notified of token request that fail to used.
     * The event listener method receives a Yokai\SecurityTokenBundle\Event\TokenAlreadyConsumedEvent instance.
     *
     * @Event
     *
     * @var string
     */
    const TOKEN_ALREADY_CONSUMED = 'yokai_security_yoken.token_already_consumed';
}
