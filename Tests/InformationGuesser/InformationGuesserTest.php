<?php

namespace Yokai\SecurityTokenBundle\Tests\InformationGuesser;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Yokai\SecurityTokenBundle\InformationGuesser\InformationGuesser;

/**
 * @author Yann Eugoné <eugone.yann@gmail.com>
 */
class InformationGuesserTest extends TestCase
{
    protected function guesser(RequestStack $requestStack): InformationGuesser
    {
        return new InformationGuesser($requestStack);
    }

    /**
     * @test
     */
    public function it_return_empty_array_if_no_master_request(): void
    {
        $requestStack = new RequestStack();

        $info = $this->guesser($requestStack)->get();

        self::assertSame([], $info);
    }

    /**
     * @test
     */
    public function it_return_array_with_ip_from_master_request(): void
    {
        $requestStack = new RequestStack();
        $requestStack->push(new Request([], [], [], [], [], ['REMOTE_ADDR' => '88.88.88.88']));

        $info = $this->guesser($requestStack)->get();

        self::assertArrayHasKey('ip', $info);
        self::assertSame('88.88.88.88', $info['ip']);
        self::assertArrayHasKey('host', $info);
    }
}
