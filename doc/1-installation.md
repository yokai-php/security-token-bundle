Installation
------------

Install process is very simple.


### Add the dependency with Composer

```bash
$ composer require yokai/security-token-bundle
```


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

The bundle requires 2 tables to store your tokens.
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
