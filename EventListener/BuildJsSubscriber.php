<?php

namespace MauticPlugin\MauticMultiDomainBundle\EventListener;

use Mautic\CoreBundle\CoreEvents;
use Mautic\CoreBundle\Event\BuildJsEvent;
use Mautic\CoreBundle\Templating\Helper\AssetsHelper;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\RouterInterface;

class BuildJsSubscriber implements EventSubscriberInterface
{

    /**
     * @var AssetsHelper
     */
    private $assetsHelper;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var RouterInterface
     */
    private $router;

    public function __construct(
        AssetsHelper $assetsHelper,
        RequestStack $requestStack,
        RouterInterface $router
    ) {
        $this->assetsHelper   = $assetsHelper;
        $this->requestStack   = $requestStack;
        $this->router         = $router;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            CoreEvents::BUILD_MAUTIC_JS => [
                ['onBuildJs', 9999],
            ],
        ];
    }

    public function onBuildJs(BuildJsEvent $event)
    {
        // PageBundle -> $pageTrackingUrl, $pageTrackingCORSUrl, $contactIdUrl
        $context = $this->router->getContext();
        $context->setHost($this->requestStack->getCurrentRequest()->getHttpHost());
        // PageBundle -> $mauticBaseUrl, $jQueryUrl, initGatedVideo
        $this->assetsHelper->setSiteUrl($this->requestStack->getCurrentRequest()->getSchemeAndHttpHost());

        // The only one we can't control now is DynamicContentBundle -> MauticDomain
        // however it uses the below, which should mirror where the request was made to - so that's cool.
        // $this->requestStack->getCurrentRequest()->getSchemeAndHttpHost();
    }
}
