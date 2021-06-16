<?php

/*
 * @copyright   2016 Mautic, Inc. All rights reserved
 * @author      Mautic, Inc
 *
 * @link        https://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticMultiDomainBundle\EventListener;

use Mautic\AssetBundle\Helper\TokenHelper as AssetTokenHelper;
use Mautic\CoreBundle\Event as MauticEvents;
use Mautic\CoreBundle\Helper\InputHelper;
use Mautic\CoreBundle\Helper\IpLookupHelper;
use Mautic\CoreBundle\Model\AuditLogModel;
use Mautic\LeadBundle\Entity\Lead;
use Mautic\LeadBundle\Helper\TokenHelper;
use Mautic\PageBundle\Entity\Trackable;
use Mautic\PageBundle\Helper\TokenHelper as PageTokenHelper;
use Mautic\PageBundle\Model\TrackableModel;
use MauticPlugin\MauticMultiDomainBundle\Event\MultidomainEvent;
use MauticPlugin\MauticMultiDomainBundle\Model\MultidomainModel;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\RouterInterface;

class MultidomianSubscriber implements EventSubscriberInterface
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var IpLookupHelper
     */
    private $ipHelper;

    /**
     * @var AuditLogModel
     */
    private $auditLogModel;

    /**
     * @var TrackableModel
     */
    private $trackableModel;

    /**
     * @var PageTokenHelper
     */
    private $pageTokenHelper;

    /**
     * @var AssetTokenHelper
     */
    private $assetTokenHelper;

    /**
     * @var MultidomainModel
     */
    private $multidomainModel;

    /**
     * @var RequestStack
     */
    private $requestStack;

    public function __construct(
        RouterInterface $router,
        IpLookupHelper $ipLookupHelper,
        AuditLogModel $auditLogModel,
        TrackableModel $trackableModel,
        PageTokenHelper $pageTokenHelper,
        AssetTokenHelper $assetTokenHelper,
        MultidomainModel $multidomainModel,
        RequestStack $requestStack
    ) {
        $this->router           = $router;
        $this->ipHelper         = $ipLookupHelper;
        $this->auditLogModel    = $auditLogModel;
        $this->trackableModel   = $trackableModel;
        $this->pageTokenHelper  = $pageTokenHelper;
        $this->assetTokenHelper = $assetTokenHelper;
        $this->multidomainModel       = $multidomainModel;
        $this->requestStack     = $requestStack;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST          => ['onKernelRequest', 0],
            'mautic.multidomain_post_save'         => ['onMultidomainPostSave', 0],
            'mautic.multidomain_delete'       => ['onMultidomainDelete', 0],
            'mautic.multidomain_replacement' => ['onTokenReplacement', 0],
        ];
    }

    /*
     * Check and hijack the form's generate link if the ID has mf- in it
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if ($event->isMasterRequest()) {
            // get the current event request
            $request    = $event->getRequest();
            $requestUri = $request->getRequestUri();

            $formGenerateUrl = $this->router->generate('mautic_form_generateform');

            if (false !== strpos($requestUri, $formGenerateUrl)) {
                $id = InputHelper::_($this->requestStack->getCurrentRequest()->get('id'));
                if (0 === strpos($id, 'mf-')) {
                    $mfId             = str_replace('mf-', '', $id);
                    $multidomainGenerateUrl = $this->router->generate('mautic_multidomain_action', ['id' => $mfId]);

                    $event->setResponse(new RedirectResponse($multidomainGenerateUrl));
                }
            }
        }
    }

    /**
     * Add an entry to the audit log.
     */
    public function onMultidomainPostSave(MultidomainEvent $event)
    {
        $entity = $event->getMultidomain();
        if ($details = $event->getChanges()) {
            $log = [
                'bundle'    => 'multidomain',
                'object'    => 'multidomain',
                'objectId'  => $entity->getId(),
                'action'    => ($event->isNew()) ? 'create' : 'update',
                'details'   => $details,
                'ipAddress' => $this->ipHelper->getIpAddressFromRequest(),
            ];
            
            $this->auditLogModel->writeToLog($log);
            
        }
    }

    /**
     * Add a delete entry to the audit log.
     */
    public function onMultidomainDelete(MultidomainEvent $event)
    {
        $entity = $event->getMultidomain();
        $log    = [
            'bundle'    => 'multidomain',
            'object'    => 'multidomain',
            'objectId'  => $entity->deletedId,
            'action'    => 'delete',
            'details'   => ['name' => $entity->getName()],
            'ipAddress' => $this->ipHelper->getIpAddressFromRequest(),
        ];
        $this->auditLogModel->writeToLog($log);
    }

    public function onTokenReplacement(MauticEvents\TokenReplacementEvent $event)
    {
        /** @var Lead $lead */
        $lead         = $event->getLead();
        $content      = $event->getContent();
        $clickthrough = $event->getClickthrough();

        if ($content) {
            $tokens = array_merge(
                $this->pageTokenHelper->findPageTokens($content, $clickthrough),
                $this->assetTokenHelper->findAssetTokens($content, $clickthrough)
            );

            if ($lead && $lead->getId()) {
                $tokens = array_merge($tokens, TokenHelper::findLeadTokens($content, $lead->getProfileFields()));
            }

            list($content, $trackables) = $this->trackableModel->parseContentForTrackables(
                $content,
                $tokens,
                'multidomain',
                $clickthrough['multidomain_id']
            );

            $multidomain = $this->multidomainModel->getEntity($clickthrough['multidomain_id']);

            /**
             * @var string
             * @var Trackable $trackable
             */
            foreach ($trackables as $token => $trackable) {
                $tokens[$token] = $this->trackableModel->generateTrackableUrl($trackable, $clickthrough, false, $multidomain->getUtmTags());
            }

            $content = str_replace(array_keys($tokens), array_values($tokens), $content);

            $event->setContent($content);
        }
    }
}
