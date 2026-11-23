Token storage
-------------

The `storage` option tells where tokens are stored.


### Doctrine ORM (default)

Tokens are stored in 2 tables, see [installation](1-installation.md#create-the-tables).
They go to the default entity manager, unless you choose another one:

```yaml
# config/packages/yokai_security_token.yaml
yokai_security_token:
    storage: orm
    orm:
        entity_manager: tokens
```

This storage requires `doctrine/orm` and `doctrine/doctrine-bundle`.


### Your own storage

Create a service that implements
[`TokenRepositoryInterface`](../src/Repository/TokenRepositoryInterface.php),
and set its id as the storage:

```yaml
# config/packages/yokai_security_token.yaml
yokai_security_token:
    storage: App\Security\Token\RedisTokenRepository
```

Your storage has to rebuild `Token` objects from what it stored.
For instance, to store tokens as JSON, normalize them with their getters,
and restore them with the constructor, then replay their usages:

```php
use DateTimeImmutable;
use Yokai\SecurityTokenBundle\Entity\Token;
use Yokai\SecurityTokenBundle\Entity\TokenUsage;

private function toJson(Token $token): string
{
    return \json_encode([
        'user_class' => $token->getUserClass(),
        'user_id' => $token->getUserId(),
        'value' => $token->getValue(),
        'purpose' => $token->getPurpose(),
        'payload' => $token->getPayload(),
        'created_information' => $token->getCreatedInformation(),
        'allowed_usages' => $token->getAllowedUsages(),
        'expires_at' => $token->getExpiresAt()->format(\DATE_ATOM),
        'keep_until' => $token->getKeepUntil()->format(\DATE_ATOM),
        'usages' => \array_map(
            static fn (TokenUsage $usage): array => [
                'information' => $usage->getInformation(),
                'created_at' => $usage->getCreatedAt()->format(\DATE_ATOM),
            ],
            $token->getUsages(),
        ),
    ], \JSON_THROW_ON_ERROR);
}

private function fromJson(string $json): Token
{
    $data = \json_decode($json, true, flags: \JSON_THROW_ON_ERROR);
    $expiresAt = new DateTimeImmutable($data['expires_at']);
    $keepUntil = new DateTimeImmutable($data['keep_until']);

    // the constructor takes durations: validity from now, keep from expiration
    $token = new Token(
        $data['user_class'],
        $data['user_id'],
        $data['value'],
        $data['purpose'],
        \sprintf('%+d seconds', $expiresAt->getTimestamp() - \time()),
        \sprintf('%+d seconds', $keepUntil->getTimestamp() - $expiresAt->getTimestamp()),
        $data['allowed_usages'],
        $data['payload'],
        $data['created_information'],
    );
    foreach ($data['usages'] as $usage) {
        $token->consume($usage['information'], new DateTimeImmutable($usage['created_at']));
    }

    return $token;
}
```

> **Note :** Restored dates are precise to the second,
> and the creation date of a restored token is the date it was restored: store it apart if you need it.

To check that your storage behaves as the bundle expects,
extend [`TokenRepositoryTestCase`](../src/Test/TokenRepositoryTestCase.php) in your tests:

```php
<?php

namespace App\Tests\Security\Token;

use App\Security\Token\RedisTokenRepository;
use Yokai\SecurityTokenBundle\Repository\TokenRepositoryInterface;
use Yokai\SecurityTokenBundle\Test\TokenRepositoryTestCase;

final class RedisTokenRepositoryTest extends TokenRepositoryTestCase
{
    protected function createRepository(): TokenRepositoryInterface
    {
        // an empty storage
        return new RedisTokenRepository(/* ... */);
    }
}
```

If your storage keeps tokens in memory between calls, override `refresh()` to clear it,
so that tests read from the storage itself.


### Without Doctrine

The bundle finds the user of a token with Doctrine.
Without it, register your own [`UserManagerInterface`](../src/Manager/UserManagerInterface.php) service,
with the `yokai_security_token.user_manager` tag.


---

« [Events](5-events.md) • [README](../README.md) »
