YokaiSecurityTokenBundle
========================

[![Latest Stable Version](https://poser.pugx.org/yokai/security-token-bundle/v/stable)](https://packagist.org/packages/yokai/security-token-bundle)
[![Latest Unstable Version](https://poser.pugx.org/yokai/security-token-bundle/v/unstable)](https://packagist.org/packages/yokai/security-token-bundle)
[![Total Downloads](https://poser.pugx.org/yokai/security-token-bundle/downloads)](https://packagist.org/packages/yokai/security-token-bundle)
[![License](https://poser.pugx.org/yokai/security-token-bundle/license)](https://packagist.org/packages/yokai/security-token-bundle)

[![Build Status](https://api.travis-ci.org/yokai-php/security-token-bundle.png?branch=master)](https://travis-ci.org/yokai-php/security-token-bundle)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/yokai-php/security-token-bundle/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/yokai-php/security-token-bundle/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/yokai-php/security-token-bundle/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/yokai-php/security-token-bundle/?branch=master)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/596d2076-90ee-49d9-a8b2-e3bcbd390874/mini.png)](https://insight.sensiolabs.com/projects/596d2076-90ee-49d9-a8b2-e3bcbd390874)

Installation
------------

### Add the bundle as dependency with Composer

``` bash
$ php composer.phar require yokai/security-token-bundle
```

### Enable the bundle in the kernel

``` php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = [
        // ...
        new Yokai\SecurityTokenBundle\YokaiSecurityTokenBundle(),
    ];
}
```


### Configuration

``` yaml
# app/config/config.yml

yokai_security_token:
    tokens:
        reset_password: ~
```

First thing is to define the User entity that your application has defined.
This way, each time a Token will be created, it will be linked automatically to it's User.

Then you can configure all the tokens your applications aims to create.
Each token can have following options :

- `generator` : a service id that implements [`Yokai\SecurityTokenBundle\Generator\TokenGeneratorInterface`](Generator/TokenGeneratorInterface)
- `duration` : a valid [`DateTime::modify`](php.net/manual/datetime.modify.php) argument

Default values fallback to :

- `generator` : [`yokai_security_token.open_ssl_token_generator`](Generator/OpenSslTokenGenerator)
- `duration` : `+2 days`


Usage
-----

``` php
<?php

namespace AppBundle\Controller;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Yokai\SecurityTokenBundle\Exception\TokenExpiredException;
use Yokai\SecurityTokenBundle\Exception\TokenNotFoundException;
use Yokai\SecurityTokenBundle\Exception\TokenUsedException;
use Yokai\SecurityTokenBundle\Manager\TokenManagerInterface;

class SecurityController extends Controller
{
    public function askResetPasswordAction(Request $request)
    {
        $user = $this->getUserRepository()->findOneByUsername($request->request->get('username'));
        if (!$user) {
            throw $this->createNotFoundException(); // or whatever you want
        }

        $this->getTokenManager()->create('reset_password', $user);

        return new Response(); // or whatever you want
    }

    public function doResetPasswordAction(Request $request)
    {
        $token = null;
        try {
            $token = $this->getTokenManager()->get('reset_password', $request->query->get('token'));
        } catch(TokenNotFoundException $e) {
            /* there is no token with the asked value */
        } catch(TokenExpiredException $e) {
            /* a token was found, but expired */
        } catch(TokenUsedException $e) {
            /* a token was found, but already used */
        }

        if (!$token) {
            throw $this->createNotFoundException(); // or whatever you want
        }

        $user = $this->getTokenManager()->getUser($token);

        $user->setPassword($request->request->get('password'));
        $this->getUserManager()->flush($user);

        $this->getTokenManager()->setUsed($token);

        return new Response(); // or whatever you want
    }

    /**
     * @return TokenManagerInterface
     */
    private function getTokenManager()
    {
        return $this->get('yokai_security_token.token_manager');
    }

    /**
     * @return EntityRepository
     */
    private function getUserRepository()
    {
        return $this->getDoctrine()->getRepository('AppBundle:User');
    }

    /**
     * @return EntityManager
     */
    private function getUserManager()
    {
        return $this->getDoctrine()->getManager();
    }
}
```

**askResetPasswordAction** :

The `Token Manager` service will handle creating a security token for you,
according to what you have configured for the purpose you asked.


**doResetPasswordAction** :

The `Token Manager` service will handle retrieving security token for you,
returning it when succeed, and throwing exceptions if something wrong :

- Token not found
- Token expired
- Token already used

The `Token Manager` service then mark the Token as used, so it cannot be used twice.


MIT License
-----------

License can be found [here](https://github.com/yokai-php/security-token-bundle/blob/master/LICENSE).


Authors
-------

The bundle was originally created by [Yann Eugoné](https://github.com/yann-eugone).

See the list of [contributors](https://github.com/yokai-php/security-token-bundle/contributors).
