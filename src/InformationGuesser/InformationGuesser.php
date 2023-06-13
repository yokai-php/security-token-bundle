<?php

declare(strict_types=1);

namespace Yokai\SecurityTokenBundle\InformationGuesser;

use Symfony\Component\HttpFoundation\RequestStack;

/**
 * This information guesser, is finding client ip and hostname
 *
 * @author Yann Eugoné <eugone.yann@gmail.com>
 */
class InformationGuesser implements InformationGuesserInterface
{
    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @param RequestStack $requestStack The request stack
     */
    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function get(): array
    {
        if (\method_exists($this->requestStack, 'getMainRequest')) {
            $request = $this->requestStack->getMainRequest();
        } else {
            $request = $this->requestStack->getMasterRequest();
        }

        if (!$request) {
            return [];
        }

        return [
            'ip' => $request->getClientIp(),
            'host' => gethostname(),
        ];
    }
}
