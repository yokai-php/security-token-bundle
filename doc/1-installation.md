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


---

« [README](../README.md) • [Configuration](2-configuration.md) »
