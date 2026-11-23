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
