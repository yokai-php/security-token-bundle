Archive command
---------------


### Command usage

This bundle provide a simple command to archive tokens, so you can keep your database clean as possible.

```bash
$ bin/console yokai:security-token:archive
```

The command is using the archivist service to perform the operation.

> **Note :** The default implementation of the service is 
> [`Yokai\SecurityTokenBundle\Archive\DeleteArchivist`](../../Archive/DeleteArchivist.php).
> This implementation just delete every outdated tokens, based on the `keepUntil` property.


### Creating your own archivist

Fist create a class that will contain your own logic and the associated service.

```php
<?php

namespace App\Security\Token\Archivist;

use Yokai\SecurityTokenBundle\Archive\ArchivistInterface;

class AppArchivist implements ArchivistInterface
{
    public function archive($purpose = null, \DateTime $before = null)
    {
        // whatever you can imagine
    }
}
```

Then you only need to tell this bundle that it should use your service instead of the default one.

```yaml
# config/packages/yokai_security_token.yaml
yokai_security_token:
    services:
        archivist: App\Security\Token\Archivist\AppArchivist
```


---

« [Usage](3-usage.md) • [Events](5-events.md) »
