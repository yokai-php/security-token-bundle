Configuration
-------------

Configure the tokens your application is managing :

```yaml
# config/packages/yokai_security_token.yaml
yokai_security_token:
    tokens:
        reset_password: ~
```

Each token can have following options :

- `generator` : a service id that implements [`Yokai\SecurityTokenBundle\Generator\TokenGeneratorInterface`](../src/Generator/TokenGeneratorInterface.php)
- `duration` : a valid [`DateTime::modify`](https://php.net/manual/datetime.modify.php) argument that represent the validity duration for tokens of this type
- `usages` : an integer that represent the number of allowed usages for tokens of this type (`0` means unlimited)
- `keep` : a valid [`DateTime::modify`](https://php.net/manual/datetime.modify.php) argument that represent the keep duration for tokens of this type
- `unique` : a boolean that indicates whether or not the token must be unique per user

Default values fallback to :

- `generator` : [`yokai_security_token.bin2hex_token_generator`](../src/Generator/Bin2HexTokenGenerator.php)
- `duration` : `+2 days`
- `usages` : `1`
- `keep` : `+1 month`
- `unique` : `false`


### Token storage

Tokens are stored with Doctrine ORM by default, in the default entity manager.
You can choose another entity manager:

```yaml
# config/packages/yokai_security_token.yaml
yokai_security_token:
    storage: orm
    orm:
        entity_manager: tokens
```

You can also store tokens elsewhere, without Doctrine.
Create a service that implements
[`Yokai\SecurityTokenBundle\Repository\TokenRepositoryInterface`](../src/Repository/TokenRepositoryInterface.php),
and set its id as the storage:

```yaml
# config/packages/yokai_security_token.yaml
yokai_security_token:
    storage: App\Security\Token\RedisTokenRepository
```

> **Note :** Without Doctrine, the bundle cannot find users from tokens:
> register your own [`UserManagerInterface`](../src/Manager/UserManagerInterface.php) service,
> with the `yokai_security_token.user_manager` tag.


---

« [Installation](1-installation.md) • [Usage](3-usage.md) »
