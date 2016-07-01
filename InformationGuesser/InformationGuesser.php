<?php

namespace Yokai\SecurityTokenBundle\InformationGuesser;

use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @author Yann Eugoné <yann.eugone@gmail.com>
 */
class InformationGuesser implements InformationGuesserInterface
{
    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @param RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * @inheritDoc
     */
    public function get()
    {
        $request = $this->requestStack->getMasterRequest();
        if (!$request) {
            return [];
        }

        return [
            'ip' => $request->getClientIp(),
            'host' => gethostname(),
        ];
    }
}
