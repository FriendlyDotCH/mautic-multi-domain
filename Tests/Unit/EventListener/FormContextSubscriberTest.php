<?php

declare(strict_types=1);

namespace MauticPlugin\MauticMultiDomainBundle\Tests\Unit\EventListener;

use Mautic\CoreBundle\Helper\CoreParametersHelper;
use Mautic\CoreBundle\Twig\Helper\AssetsHelper;
use MauticPlugin\MauticMultiDomainBundle\EventListener\FormContextSubscriber;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Asset\Packages;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouterInterface;

class FormContextSubscriberTest extends TestCase
{
    private AssetsHelper $assetsHelper;
    /** @var RouterInterface&MockObject */
    private RouterInterface $router;
    private RequestContext $routerContext;
    private FormContextSubscriber $subscriber;

    protected function setUp(): void
    {
        $this->assetsHelper = new AssetsHelper(
            $this->createMock(Packages::class),
            $this->createMock(CoreParametersHelper::class)
        );
        $this->assetsHelper->setSiteUrl('https://mautic.example.com');

        $this->routerContext = new RequestContext();
        $this->routerContext->setHost('mautic.example.com');

        $this->router = $this->createMock(RouterInterface::class);
        $this->router->method('getContext')->willReturn($this->routerContext);

        $this->subscriber = new FormContextSubscriber($this->assetsHelper, $this->router);
    }

    /**
     * @param array<string, mixed> $params
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('provideMatchingRoutes')]
    public function testSetsSiteUrlForMatchingRoutes(string $url, string $route, array $params, string $expectedHost): void
    {
        $request = Request::create($url);
        $event   = $this->createMainRequestEvent($request, $route, $params);

        $this->subscriber->onKernelRequest($event);

        self::assertSame($expectedHost, $this->assetsHelper->getBaseUrl());
        self::assertSame(str_replace(['http://', 'https://'], '', $expectedHost), $this->routerContext->getHost());
    }

    /**
     * @return \Generator<string, array{string, string, array<string, mixed>, string}>
     */
    public static function provideMatchingRoutes(): \Generator
    {
        yield 'form generate.js' => [
            'http://trk.example.com/form/generate.js',
            'mautic_form_generateform',
            [],
            'http://trk.example.com',
        ];
        yield 'form generate with query' => [
            'http://trk.example.com/form/generate.js?id=42',
            'mautic_form_generateform',
            [],
            'http://trk.example.com',
        ];
        yield 'form preview' => [
            'https://trk.example.com/s/forms/preview/1',
            'mautic_form_preview',
            ['id' => 1],
            'https://trk.example.com',
        ];
        yield 'form view action' => [
            'https://trk.example.com/s/forms/view/999',
            'mautic_form_action',
            ['objectAction' => 'view', 'objectId' => 999],
            'https://trk.example.com',
        ];
    }

    /**
     * @param array<string, mixed> $params
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('provideNonMatchingRoutes')]
    public function testDoesNotModifyContextForNonMatchingRoutes(?string $route, array $params, string $url): void
    {
        $request = Request::create($url);
        $event   = $this->createMainRequestEvent($request, $route, $params);

        $initialBaseUrl = $this->assetsHelper->getBaseUrl();
        $initialHost    = $this->routerContext->getHost();

        $this->subscriber->onKernelRequest($event);

        self::assertSame($initialBaseUrl, $this->assetsHelper->getBaseUrl());
        self::assertSame($initialHost, $this->routerContext->getHost());
    }

    /**
     * @return \Generator<string, array{array<string, mixed>}|array{null, array<string, mixed>, string}|array{string, array<string, mixed>, string}>
     */
    public static function provideNonMatchingRoutes(): \Generator
    {
        yield 'no route set (e.g., early request)' => [null, [], 'http://trk.example.com/'];
        yield 'homepage' => ['mautic_core_index', [], 'http://trk.example.com/'];
        yield 'form list' => ['mautic_form_index', [], 'http://trk.example.com/s/forms'];
        yield 'form new' => ['mautic_form_action', ['objectAction' => 'new'], 'http://trk.example.com/s/forms/new'];
        yield 'form edit' => ['mautic_form_action', ['objectAction' => 'edit', 'objectId' => 1], 'http://trk.example.com/s/forms/edit/1'];
        yield 'contact view' => ['mautic_contact_view', [], 'http://trk.example.com/s/contacts/view/1'];
    }

    public function testIgnoresSubRequests(): void
    {
        $request = Request::create('http://trk.example.com/form/generate.js');
        $request->attributes->set('_route', 'mautic_form_generateform');
        $kernel = $this->createMock(HttpKernelInterface::class);
        $event  = new RequestEvent($kernel, $request, HttpKernelInterface::SUB_REQUEST);

        $initialBaseUrl = $this->assetsHelper->getBaseUrl();

        $this->subscriber->onKernelRequest($event);

        self::assertSame($initialBaseUrl, $this->assetsHelper->getBaseUrl());
    }

    /**
     * @param array<string, mixed> $params
     */
    private function createMainRequestEvent(Request $request, ?string $route = null, array $params = []): RequestEvent
    {
        if (null !== $route) {
            $request->attributes->set('_route', $route);
            foreach ($params as $key => $value) {
                $request->attributes->set($key, $value);
            }
        }

        return new RequestEvent(
            $this->createMock(HttpKernelInterface::class),
            $request,
            HttpKernelInterface::MAIN_REQUEST
        );
    }
}
