Events
------

The bundle is dispatching event at some points of the token life time.

> **Note :** Event names are stored in constants located in [TokenEvents](../../TokenEvents.php).


### When creating a token

Whenever you call `Yokai\SecurityTokenBundle\Manager\TokenManagerInterface::create` :

- `TokenEvents::CREATE_TOKEN` : [CreateTokenEvent](../../Event/CreateTokenEvent.php)
- `TokenEvents::TOKEN_CREATED` : [TokenCreatedEvent](../../Event/TokenCreatedEvent.php)


### When retrieving a token

Whenever you call `Yokai\SecurityTokenBundle\Manager\TokenManagerInterface::get` :

- `TokenEvents::TOKEN_NOT_FOUND` : [TokenNotFoundEvent](../../Event/TokenNotFoundEvent.php)
- `TokenEvents::TOKEN_EXPIRED` : [TokenExpiredEvent](../../Event/TokenExpiredEvent.php)
- `TokenEvents::TOKEN_ALREADY_CONSUMED` : [TokenAlreadyConsumedEvent](../../Event/TokenAlreadyConsumedEvent.php)
- `TokenEvents::TOKEN_RETRIEVED` : [TokenRetrievedEvent](../../Event/TokenRetrievedEvent.php)


### When consuming a token

Whenever you call `Yokai\SecurityTokenBundle\Manager\TokenManagerInterface::consume` :

- `TokenEvents::CONSUME_TOKEN` : [ConsumeTokenEvent](../../Event/ConsumeTokenEvent.php)
- `TokenEvents::TOKEN_CONSUMED` : [TokenConsumedEvent](../../Event/TokenConsumedEvent.php)
- `TokenEvents::TOKEN_TOTALLY_CONSUMED` : [TokenTotallyConsumedEvent](../../Event/TokenTotallyConsumedEvent.php)


### Subscribe to events

Subscribing to these events is as simple as registering an event listener/subscriber to Symfony's event dispatcher.

For example, lets say that you want to log errors during token retrieval, you can register a listener like this one :

```php
<?php

namespace App\EventListener;

use Psr\Log\LoggerInterface;
use Symfony\Contracts\EventDispatcher\EventSubscriberInterface;
use Yokai\SecurityTokenBundle\TokenEvents;
use Yokai\SecurityTokenBundle\Event as SecurityTokenEvents;

class LogSecurityTokenErrors implements EventSubscriberInterface
{
    /** @var LoggerInterface */
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public static function getSubscribedEvents()
    {
        return [
            TokenEvents::TOKEN_NOT_FOUND => 'onTokenNotFound',
            TokenEvents::TOKEN_EXPIRED => 'onTokenExpired',
            TokenEvents::TOKEN_ALREADY_CONSUMED => 'onTokenConsumed',
        ];
    }

    public function onTokenNotFound(SecurityTokenEvents\TokenNotFoundEvent $event)
    {
        $this->logger->warning(
            'Security token was not found',
            ['purpose' => $event->getPurpose(), 'value' => $event->getValue()]
        );
    }

    public function onTokenExpired(SecurityTokenEvents\TokenExpiredEvent $event)
    {
        $this->logger->warning(
            'Security token was expired',
            ['purpose' => $event->getPurpose(), 'value' => $event->getValue()]
        );
    }

    public function onTokenConsumed(SecurityTokenEvents\TokenAlreadyConsumedEvent $event)
    {
        $this->logger->warning(
            'Security token was already consumed',
            ['purpose' => $event->getPurpose(), 'value' => $event->getValue()]
        );
    }
}
```


---

« [Archive command](4-archive-command.md) • [README](../../README.md) »
