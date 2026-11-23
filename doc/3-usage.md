Usage
-----

The following example is about allowing unauthenticated user to recover a lost password.
It assumes a Doctrine entity `App\Entity\User`, with a `username` property and a `setPassword()` method.

```php
<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Yokai\SecurityTokenBundle\Exception\TokenConsumedException;
use Yokai\SecurityTokenBundle\Exception\TokenExpiredException;
use Yokai\SecurityTokenBundle\Exception\TokenNotFoundException;
use Yokai\SecurityTokenBundle\Manager\TokenManagerInterface;

final class SecurityController extends AbstractController
{
    public function __construct(
        private readonly TokenManagerInterface $tokenManager,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    #[Route('/password/request', name: 'password_request', methods: ['POST'])]
    public function requestPasswordRecovery(Request $request): Response
    {
        $user = $this->entityManager->getRepository(User::class)
            ->findOneBy(['username' => $request->request->getString('username')]);
        if ($user === null) {
            throw $this->createNotFoundException(); // or whatever you want
        }

        $token = $this->tokenManager->create('reset_password', $user);

        // Probably send an email with a link to the action on which the user can recover his password (see below).
        // The token value must be included in the link, so you can verify it:
        // $this->generateUrl('password_recover', ['token' => $token->getValue()], UrlGeneratorInterface::ABSOLUTE_URL)

        return new Response(); // or whatever you want
    }

    #[Route('/password/recover', name: 'password_recover', methods: ['POST'])]
    public function recoverPassword(Request $request): Response
    {
        try {
            $token = $this->tokenManager->get('reset_password', $request->query->getString('token'));
        } catch (TokenNotFoundException) {
            throw $this->createNotFoundException(); // there is no token with the asked value
        } catch (TokenExpiredException) {
            throw $this->createNotFoundException(); // a token was found, but expired
        } catch (TokenConsumedException) {
            throw $this->createNotFoundException(); // a token was found, but already consumed
        }

        /** @var User $user */
        $user = $this->tokenManager->getUser($token);

        $user->setPassword($request->request->getString('password')); // hash it in a real application
        $this->entityManager->flush();

        $this->tokenManager->consume($token);

        return new Response(); // or whatever you want
    }
}
```

**requestPasswordRecovery** :

The `Token Manager` service will handle creating a security token for you,
according to what you have configured for the purpose you asked.


**recoverPassword** :

The `Token Manager` service will handle retrieving security token for you,
returning it when succeed, and throwing exceptions if something wrong :

- Token not found
- Token expired
- Token already used

The `Token Manager` service then consume the Token, so it cannot be used twice (because we configured it like this).


---

« [Configuration](2-configuration.md) • [Archive command](4-archive-command.md) »
