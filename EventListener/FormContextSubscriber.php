<?php

declare(strict_types=1);

namespace MauticPlugin\MauticMultiDomainBundle\EventListener;

use Mautic\CoreBundle\Twig\Helper\AssetsHelper;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\RouterInterface;

class FormContextSubscriber implements EventSubscriberInterface
{
    private const FORM_GENERATE_ROUTE = 'mautic_form_generateform';
    private const FORM_PREVIEW_ROUTE = 'mautic_form_preview';
    private const FORM_ACTION_ROUTE = 'mautic_form_action';

    public function __construct(
        private AssetsHelper $assetsHelper,
        private RouterInterface $router
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 10],
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();

        if (!$this->requiresDynamicContext($request)) {
            return;
        }

        $this->setContext($request);
    }

    /**
     * Determines if the current route needs on-the-fly asset URL fixes.
     * This ensures form JS/HTML generated via Twig uses the request's actual
     * host instead of the configured site_url.
     */
    private function requiresDynamicContext(Request $request): bool
    {
        $route = $request->attributes->get('_route');

        if ($route === null) {
            return false;
        }

        return match ($route) {
            self::FORM_GENERATE_ROUTE => true,
            self::FORM_PREVIEW_ROUTE => true,
            self::FORM_ACTION_ROUTE => $request->attributes->get('objectAction') === 'view',
            default => false,
        };
    }

    private function setContext(Request $request): void
    {
        $this->assetsHelper->setSiteUrl($request->getSchemeAndHttpHost());
        $this->router->getContext()->setHost($request->getHttpHost());
    }
}
