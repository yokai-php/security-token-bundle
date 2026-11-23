Installation
------------

Install process is very simple.


### Add the dependency with Composer

```bash
$ composer require yokai/security-token-bundle
```

Tokens are stored with Doctrine ORM by default, which is not installed along with the bundle:

```bash
$ composer require doctrine/orm doctrine/doctrine-bundle
```

> **Note :** You can store tokens elsewhere, see [token storage](6-storage.md).


### Enable the bundle

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
$ bin/console doctrine:migrations:diff
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
