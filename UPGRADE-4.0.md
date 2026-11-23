# UPGRADE FROM 3.x TO 4.0

## Requirements

- PHP `>= 8.1` is now required.
- Symfony `^6.4`, `^7.4` or `^8.0` is now required.
- Doctrine ORM `^3.0` is required by the default `orm` storage (ORM 2.x is not supported anymore).
- `ext-openssl` is not required anymore.

## Doctrine is optional

`doctrine/orm` and `doctrine/doctrine-bundle` are not required by the bundle anymore.
The default `orm` storage still uses them: if your project does not require them already, add them:

```bash
composer require doctrine/orm doctrine/doctrine-bundle
```

## Token storage

The `services.token_repository` option was replaced by `storage`, which is either `orm` (default),
or the id of your own `TokenRepositoryInterface` service:

```diff
 yokai_security_token:
-    services:
-        token_repository: App\Security\Token\RedisTokenRepository
+    storage: App\Security\Token\RedisTokenRepository
```

The `yokai_security_token.default_token_repository` service was renamed `yokai_security_token.orm_token_repository`.
Prefer the `yokai_security_token.token_repository` alias, or `TokenRepositoryInterface` for autowiring.

The Doctrine ORM storage can now use another entity manager than the default one, with the `orm.entity_manager` option.

## Token generator

`Yokai\SecurityTokenBundle\Generator\OpenSslTokenGenerator` and its service
`yokai_security_token.open_ssl_token_generator` were removed.
They are replaced by `Yokai\SecurityTokenBundle\Generator\Bin2HexTokenGenerator`
and its service `yokai_security_token.bin2hex_token_generator`, which is the new default generator.

If you configured the generator explicitly, update your configuration:

```diff
 yokai_security_token:
     tokens:
         reset_password:
-            generator: yokai_security_token.open_ssl_token_generator
+            generator: yokai_security_token.bin2hex_token_generator
```

New tokens are now 64 hexadecimal characters long (they were 43 URL-safe base64 characters).
Tokens created before the upgrade remain valid: they are looked up by value, not by format.

## Events

Event getters were replaced by public readonly properties:

```diff
 public function onTokenNotFound(TokenNotFoundEvent $event): void
 {
-    $this->logger->warning('Token not found', ['purpose' => $event->getPurpose(), 'value' => $event->getValue()]);
+    $this->logger->warning('Token not found', ['purpose' => $event->purpose, 'value' => $event->value]);
 }
```

| Event                                                                              | Removed getters                                | Properties                        |
|------------------------------------------------------------------------------------|------------------------------------------------|-----------------------------------|
| `CreateTokenEvent`                                                                 | `getPurpose()`, `getUser()`                    | `$purpose`, `$user`               |
| `ConsumeTokenEvent`                                                                | `getToken()`, `getAt()`, `getInformation()`    | `$token`, `$at`, `$information`   |
| `TokenNotFoundEvent`, `TokenExpiredEvent`, `TokenAlreadyConsumedEvent`             | `getPurpose()`, `getValue()`                   | `$purpose`, `$value`              |
| `TokenCreatedEvent`, `TokenRetrievedEvent`, `TokenConsumedEvent`, `TokenTotallyConsumedEvent` | `getToken()`                          | `$token`                          |

`CreateTokenEvent::getPayload()`, `setPayload()` and `addPayload()` are unchanged.

## Token configuration

`Yokai\SecurityTokenBundle\Configuration\TokenConfiguration` getters were replaced by public readonly properties:
`getPurpose()` → `$purpose`, `getGenerator()` → `$generator`, `getDuration()` → `$duration`,
`getUsages()` → `$usages`, `getKeep()` → `$keep`, `isUnique()` → `$unique`.

## Interfaces

Native types were added to the following interfaces. Your implementations must use the same signatures:

```php
// Yokai\SecurityTokenBundle\Archive\ArchivistInterface
public function archive(string|null $purpose = null): int;

// Yokai\SecurityTokenBundle\Factory\TokenFactoryInterface
public function create(mixed $user, string $purpose, array $payload = []): Token;

// Yokai\SecurityTokenBundle\Manager\TokenManagerInterface
public function create(string $purpose, mixed $user, array $payload = []): Token;
public function consume(Token $token, DateTime|null $at = null): void;
public function getUser(Token $token): mixed;

// Yokai\SecurityTokenBundle\Manager\UserManagerInterface
public function supportsUser(mixed $user): bool;
public function getClass(mixed $user): string;
public function getId(mixed $user): string;

// Yokai\SecurityTokenBundle\Repository\TokenRepositoryInterface
public function findExisting(string $userClass, string $userId, string $purpose): Token|null;
```

`TokenRepositoryInterface` also has a new method, which removes outdated tokens (and their usages) from storage:

```php
public function purge(DateTimeInterface $before, string|null $purpose = null): int;
```

The default archivist, `Yokai\SecurityTokenBundle\Archive\DeleteArchivist`, now relies on it
instead of querying the Doctrine entity repository: the archive command works with any token repository.

## Final classes

All concrete classes of the bundle are now `final`, except the `Token` and `TokenUsage` entities.
If you extended one of them, implement the matching interface instead,
and register your service through the `services` configuration:

```yaml
yokai_security_token:
    services:
        token_manager: App\Security\TokenManager
```

`Yokai\SecurityTokenBundle\Exception\InvalidTokenException` is now `abstract`:
catching it still works, but it cannot be thrown directly anymore.

## Service definitions

Services are now defined in `config/services.php` (was `config/services.xml`).
Service ids are unchanged, apart from the token generator and the ORM token repository described above.
