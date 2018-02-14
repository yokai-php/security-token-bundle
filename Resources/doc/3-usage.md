Usage
-----

The following example is about allowing unauthenticated user to recover a lost password.

```php
<?php

namespace AppBundle\Controller;

use App\Entity\User;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Yokai\SecurityTokenBundle\Exception as TokenExceptions;
use Yokai\SecurityTokenBundle\Manager\TokenManagerInterface;

class SecurityController extends Controller
{
    /** @var TokenManagerInterface */
    private $tokenManager;
    /** @var ManagerRegistry */
    private $doctrine;
    public function __construct(TokenManagerInterface $tokenManager, ManagerRegistry $doctrine)
    {
        $this->tokenManager = $tokenManager;
        $this->doctrine = $doctrine;
    }
    
    public function requestPasswordRecoveryAction(Request $request): Response
    {
        $user = $this->getUserRepository()->findOneBy(['username' => $request->request->get('username')]);
        if (!$user) {
            throw $this->createNotFoundException(); // or whatever you want
        }

        $token = $this->tokenManager->create('reset_password', $user);

        // Probably send an email with a link to the action on which the user can recover his password (see below)
        // the token value should be included in the link so you can verify it.

        return new Response(); // or whatever you want
    }

    public function recoverPasswordAction(Request $request): Response
    {
        $token = null;
        try {
            $token = $this->tokenManager->get('reset_password', $request->query->get('token'));
        } catch(TokenExceptions\TokenNotFoundException $e) {
            /* there is no token with the asked value */
        } catch(TokenExceptions\TokenExpiredException $e) {
            /* a token was found, but expired */
        } catch(TokenExceptions\TokenConsumedException $e) {
            /* a token was found, but already consumed */
        }

        if (!$token) {
            throw $this->createNotFoundException(); // or whatever you want
        }

        $user = $this->tokenManager->getUser($token);

        $user->setPassword($request->request->get('password'));
        $this->getUserManager()->flush();

        $this->tokenManager->consume($token);

        return new Response(); // or whatever you want
    }

    private function getUserRepository(): ObjectRepository
    {
        return $this->doctrine->getRepository(User::class);
    }

    private function getUserManager(): ObjectManager
    {
        return $this->doctrine->getManagerForClass(User::class);
    }
}
```

**requestPasswordRecoveryAction** :

The `Token Manager` service will handle creating a security token for you,
according to what you have configured for the purpose you asked.


**recoverPasswordAction** :

The `Token Manager` service will handle retrieving security token for you,
returning it when succeed, and throwing exceptions if something wrong :

- Token not found
- Token expired
- Token already used

The `Token Manager` service then consume the Token, so it cannot be used twice (because we configured it like this).


---

« [Configuration](2-configuration.md) • [Archive command](4-archive-command.md) »
