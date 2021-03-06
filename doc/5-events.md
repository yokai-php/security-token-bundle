Events
------

The bundle is dispatching event at some points of the token life time.


### When creating a token

Whenever you call `Yokai\SecurityTokenBundle\Manager\TokenManagerInterface::create` :

- **Before** the token is created : [CreateTokenEvent](../src/Event/CreateTokenEvent.php)
- **After** the token is created : [TokenCreatedEvent](../src/Event/TokenCreatedEvent.php)


### When retrieving a token

Whenever you call `Yokai\SecurityTokenBundle\Manager\TokenManagerInterface::get` :

- When token is **not found** : [TokenNotFoundEvent](../src/Event/TokenNotFoundEvent.php)
- When token is **expired** : [TokenExpiredEvent](../src/Event/TokenExpiredEvent.php)
- When token is **already consumed** : [TokenAlreadyConsumedEvent](../src/Event/TokenAlreadyConsumedEvent.php)
- When token is **valid** : [TokenRetrievedEvent](../src/Event/TokenRetrievedEvent.php)


### When consuming a token

Whenever you call `Yokai\SecurityTokenBundle\Manager\TokenManagerInterface::consume` :

- **Before** token is consumed : [ConsumeTokenEvent](../src/Event/ConsumeTokenEvent.php)
- **After** token is consumed : [TokenConsumedEvent](../src/Event/TokenConsumedEvent.php)
- **After** token is totally consumed (not usable again) : [TokenTotallyConsumedEvent](../src/Event/TokenTotallyConsumedEvent.php)


### Subscribe to events

Subscribing to these events is as simple as registering an event listener/subscriber to Symfony's event dispatcher.

For example, lets say that you want to log errors during token retrieval, you can register a listener like this one :

```php
<?php

namespace App\EventListener;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Yokai\SecurityTokenBundle\Event as SecurityTokenEvents;

class LogSecurityTokenErrors implements EventSubscriberInterface
{
    /** @var LoggerInterface */
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            SecurityTokenEvents\TokenNotFoundEvent::class => 'onTokenNotFound',
            SecurityTokenEvents\TokenExpiredEvent::class => 'onTokenExpired',
            SecurityTokenEvents\TokenAlreadyConsumedEvent::class => 'onTokenConsumed',
        ];
    }

    public function onTokenNotFound(SecurityTokenEvents\TokenNotFoundEvent $event): void
    {
        $this->logger->warning(
            'Security token was not found',
            ['purpose' => $event->getPurpose(), 'value' => $event->getValue()]
        );
    }

    public function onTokenExpired(SecurityTokenEvents\TokenExpiredEvent $event): void
    {
        $this->logger->warning(
            'Security token was expired',
            ['purpose' => $event->getPurpose(), 'value' => $event->getValue()]
        );
    }

    public function onTokenConsumed(SecurityTokenEvents\TokenAlreadyConsumedEvent $event): void
    {
        $this->logger->warning(
            'Security token was already consumed',
            ['purpose' => $event->getPurpose(), 'value' => $event->getValue()]
        );
    }
}
```


---

« [Archive command](4-archive-command.md) • [README](../README.md) »
