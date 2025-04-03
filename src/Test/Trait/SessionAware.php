<?php

namespace GrinWay\Service\Test\Trait;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\HttpFoundation\Session\Storage\MockFileSessionStorage;

trait SessionAware
{
    protected static function setUpUnitTestSession(): void
    {
        $sessionMock = new Session(new MockArraySessionStorage());
        $request = new Request();
        $request->setSession($sessionMock);
        $requestStack = self::getContainer()->get(RequestStack::class);
        $requestStack->push($request);
    }

    protected static function setUpFunctionalTestSession(): void
    {
        $sessionMock = new Session(new MockFileSessionStorage());
        $request = new Request();
        $request->setSession($sessionMock);
        $requestStack = self::getContainer()->get(RequestStack::class);
        $requestStack->push($request);
    }
}
