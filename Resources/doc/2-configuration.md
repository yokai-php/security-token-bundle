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

- `generator` : a service id that implements [`Yokai\SecurityTokenBundle\Generator\TokenGeneratorInterface`](../../Generator/TokenGeneratorInterface.php)
- `duration` : a valid [`DateTime::modify`](https://php.net/manual/datetime.modify.php) argument that represent the validity duration for tokens of this type
- `usages` : an integer that represent the number of allowed usages for tokens of this type
- `keep` : a valid [`DateTime::modify`](https://php.net/manual/datetime.modify.php) argument that represent the keep duration for tokens of this type
- `unique` : a boolean that indicates whether or not the token must be unique per user

Default values fallback to :

- `generator` : [`yokai_security_token.open_ssl_token_generator`](../../Generator/OpenSslTokenGenerator.php)
- `duration` : `+2 days`
- `usages` : `1`
- `keep` : `+1 month`
- `unique` : `false`


---

« [Installation](1-installation.md) • [Usage](3-usage.md) »
