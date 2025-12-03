<?php

declare(strict_types=1);

namespace Yokai\SecurityTokenBundle\InformationGuesser;

use Symfony\Component\HttpFoundation\RequestStack;

/**
 * This information guesser, is finding client ip and hostname
 *
 * @author Yann Eugoné <eugone.yann@gmail.com>
 */
final class InformationGuesser implements InformationGuesserInterface
{
    public function __construct(
        private RequestStack $requestStack,
    ) {
    }

    public function get(): array
    {
        $request = $this->requestStack->getMainRequest();
        if ($request === null) {
            return [];
        }

        return [
            'ip' => $request->getClientIp(),
            'host' => \gethostname(),
        ];
    }
}
