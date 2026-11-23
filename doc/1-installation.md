Installation
------------

Install process is very simple.


### Add the dependency with Composer

Tokens are stored with Doctrine ORM by default, which is not installed along with the bundle.
Install them together:

```bash
$ composer require yokai/security-token-bundle doctrine/orm doctrine/doctrine-bundle
```

> **Note :** You can store tokens elsewhere, without Doctrine, see [token storage](6-storage.md).
> Until the `storage` option is configured, the bundle requires Doctrine,
> so the `cache:clear` run by Composer fails: configure it, then clear the cache.


### Enable the bundle

With [Symfony Flex](https://symfony.com/doc/current/setup/flex.html), the bundle is enabled for you.
Otherwise, add it to `config/bundles.php`:

```php
<?php
// config/bundles.php

return [
    // ...
    Yokai\SecurityTokenBundle\YokaiSecurityTokenBundle::class => ['all' => true],
];
```

### Create the tables

With the default `orm` storage, the bundle requires 2 tables to store your tokens.
You need to create these in your project:

**Using a [doctrine migration](https://github.com/doctrine/DoctrineMigrationsBundle)** (recommended)
```bash
$ composer require doctrine/doctrine-migrations-bundle # if not installed yet
$ bin/console doctrine:migrations:diff
$ bin/console doctrine:migrations:migrate
```

You will have a migration file generated with the following tables being created:
```sql
CREATE TABLE yokai_security_token ...
CREATE TABLE yokai_security_token_usage ...
```

**Using the doctrine schema update command** (not recommended)
```bash
$ bin/console doctrine:schema:update --force
```


---

« [README](../README.md) • [Configuration](2-configuration.md) »
