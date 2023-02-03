<?php

/*
 * @copyright   2014 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticMultiDomainBundle\EventListener;

use Doctrine\ORM\EntityManager;
use Mautic\CoreBundle\Form\Type\SlotButtonType;
use Mautic\CoreBundle\Form\Type\SlotCodeModeType;
use Mautic\CoreBundle\Form\Type\SlotDynamicContentType;
use Mautic\CoreBundle\Form\Type\SlotImageCaptionType;
use Mautic\CoreBundle\Form\Type\SlotImageCardType;
use Mautic\CoreBundle\Form\Type\SlotSeparatorType;
use Mautic\CoreBundle\Form\Type\SlotSocialFollowType;
use Mautic\CoreBundle\Form\Type\SlotTextType;
use Mautic\CoreBundle\Helper\CoreParametersHelper;
use Mautic\CoreBundle\Helper\EmojiHelper;
use Mautic\EmailBundle\EmailEvents;
use Mautic\EmailBundle\Entity\Email;
use Mautic\EmailBundle\Event\EmailBuilderEvent;
use Mautic\EmailBundle\Event\EmailSendEvent;
use Mautic\EmailBundle\Model\EmailModel;
use Mautic\PageBundle\Entity\Redirect;
use Mautic\PageBundle\Entity\Trackable;
use Mautic\PageBundle\Model\RedirectModel;
use Mautic\PageBundle\Model\TrackableModel;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Translation\TranslatorInterface;
use MauticPlugin\MauticMultiDomainBundle\Model\MultidomainModel;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class BuilderSubscriber implements EventSubscriberInterface
{
    /**
     * @var CoreParametersHelper
     */
    private $coreParametersHelper;

    /**
     * @var EmailModel
     */
    private $emailModel;

    /**
     * @var TrackableModel
     */
    private $pageTrackableModel;

    /**
     * @var RedirectModel
     */
    private $pageRedirectModel;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var MultidomainModel
     */
    private $multidomainModel;

    /**
     * @var RouterInterface
     */
    private $router;

    public function __construct(
        CoreParametersHelper $coreParametersHelper,
        EmailModel $emailModel,
        TrackableModel $trackableModel,
        RedirectModel $redirectModel,
        TranslatorInterface $translator,
        EntityManager $entityManager,
        MultidomainModel $multidomainModel,
        RouterInterface $router
    ) {
        $this->coreParametersHelper = $coreParametersHelper;
        $this->emailModel           = $emailModel;
        $this->pageTrackableModel   = $trackableModel;
        $this->pageRedirectModel    = $redirectModel;
        $this->translator           = $translator;
        $this->entityManager        = $entityManager;
        $this->multidomainModel     = $multidomainModel;
        $this->router               = $router;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [           
            EmailEvents::EMAIL_ON_SEND  => [
                ['onEmailGenerate', 0],
                // Ensure this is done last in order to catch all tokenized URLs
                ['convertUrlsToTokens', -9999],
            ],
            EmailEvents::EMAIL_ON_DISPLAY => [
                ['onEmailGenerate', -999],
                // Ensure this is done last in order to catch all tokenized URLs
                ['convertUrlsToTokens', -9999],
            ],
        ];
    }

    public function onEmailGenerate(EmailSendEvent $event)
    {        
        $idHash = $event->getIdHash();
        $lead   = $event->getLead();
        $email  = $event->getEmail();
        $senderEmail = null;
        $senderDomain = null; 

        if (null == $idHash) {
            // Generate a bogus idHash to prevent errors for routes that may include it
            $idHash = uniqid();
        }
        
        $unsubscribeText = $this->coreParametersHelper->get('unsubscribe_text');
        $webviewText = $this->coreParametersHelper->get('webview_text');
        
        // Check if the Mailer is Owner is set, if not then search the sender email from Email channels From address
        // And finally if nothing is set, use the mailer from email if it is not blank.
        if($this->coreParametersHelper->get('mailer_is_owner') && $lead['id']){
            $senderEmail = $lead['email'];
        }else if($email && $email->getFromAddress()){
            $senderEmail = $email->getFromAddress();
        }else{
            $senderEmail = $this->coreParametersHelper->get("mailer_from_email");
        }

        if($senderEmail){
            // Get the Domain from the MultiDomain Configuration by email.
            $multiDomian = $this->multidomainModel->getRepository()->findOneBy(['email' => $senderEmail]);
            if($multiDomian){
                $senderDomain = $multiDomian->getDomain();
            }
        }
        
        if (!$unsubscribeText) {
            $unsubscribeText = $this->translator->trans('mautic.email.unsubscribe.text', ['%link%' => '|URL|']);
        }
        $unsubscribeText = str_replace('|URL|', $this->buildUrl('mautic_email_unsubscribe', ['idHash' => $idHash], true, [], [], $senderDomain), $unsubscribeText);
        $event->addToken('{unsubscribe_text}', EmojiHelper::toHtml($unsubscribeText));

        $event->addToken('{unsubscribe_url}', $this->buildUrl('mautic_email_unsubscribe', ['idHash' => $idHash], true, [], [], $senderDomain));

        
        if (!$webviewText) {
            $webviewText = $this->translator->trans('mautic.email.webview.text', ['%link%' => '|URL|']);
        }
        $webviewText = str_replace('|URL|', $this->buildUrl('mautic_email_preview', ['idHash' => $idHash], true, [], [], $senderDomain), $webviewText);
        $event->addToken('{webview_text}', EmojiHelper::toHtml($webviewText));

        // Show public email preview if the lead is not known to prevent 404
        if (empty($lead['id']) && $email) {
            $event->addToken('{webview_url}', $this->buildUrl('mautic_email_preview', ['objectId' => $email->getId()], true, [], [], $senderDomain));
        } else {
            $event->addToken('{webview_url}', $this->buildUrl('mautic_email_webview', ['idHash' => $idHash], true, [], [], $senderDomain));
        }

        $signatureText = $this->coreParametersHelper->get('default_signature_text');
        $fromName      = $this->coreParametersHelper->get('mailer_from_name');
        $signatureText = str_replace('|FROM_NAME|', $fromName, nl2br($signatureText));
        $event->addToken('{signature}', EmojiHelper::toHtml($signatureText));
        $event->addToken('{subject}', EmojiHelper::toHtml($event->getSubject()));
    }

    /**
     * @return array
     */
    public function convertUrlsToTokens(EmailSendEvent $event)
    {
        if ($event->isInternalSend() || $this->coreParametersHelper->get('disable_trackable_urls')) {
            // Don't convert urls
            return;
        }

        $email   = $event->getEmail();
        $emailId = ($email) ? $email->getId() : null;
        if (!$email instanceof Email) {
            $email = $this->emailModel->getEntity($emailId);
        }

        $utmTags      = $email->getUtmTags();
        $clickthrough = $event->generateClickthrough();
        $trackables   = $this->parseContentForUrls($event, $emailId);

        /**
         * @var string
         * @var Trackable $trackable
         */
        foreach ($trackables as $token => $trackable) {
            $url = ($trackable instanceof Trackable)
                ?
                $this->pageTrackableModel->generateTrackableUrl($trackable, $clickthrough, false, $utmTags)
                :
                $this->pageRedirectModel->generateRedirectUrl($trackable, $clickthrough, false, $utmTags);

            $event->addToken($token, $url);
        }
    }

    /**
     * Parses content for URLs and tokens.
     *
     * @param $emailId
     *
     * @return mixed
     */
    private function parseContentForUrls(EmailSendEvent $event, $emailId)
    {
        static $convertedContent = [];

        // Prevent parsing the exact same content over and over
        if (!isset($convertedContent[$event->getContentHash()])) {
            $html = $event->getContent();
            $text = $event->getPlainText();

            $contentTokens = $event->getTokens();

            [$content, $trackables] = $this->pageTrackableModel->parseContentForTrackables(
                [$html, $text],
                $contentTokens,
                ($emailId) ? 'email' : null,
                $emailId
            );

            [$html, $text] = $content;
            unset($content);

            if ($html) {
                $event->setContent($html);
            }
            if ($text) {
                $event->setPlainText($text);
            }

            $convertedContent[$event->getContentHash()] = $trackables;

            // Don't need to preserve Trackable or Redirect entities in memory
            $this->entityManager->clear(Redirect::class);
            $this->entityManager->clear(Trackable::class);

            unset($html, $text, $trackables);
        }

        return $convertedContent[$event->getContentHash()];
    }

    private function buildUrl(
        $route, 
        $routeParams = [], 
        $absolute = true, 
        $clickthrough = [], 
        $utmTags = [], 
        $domain = null
    )
    {
        
        
        if($domain){
            $parseUrl = parse_url($domain);
            $context = $this->router->getGenerator()->getContext();
            $context->setHost($parseUrl['host']);
            $context->setScheme($parseUrl['scheme']);
        }

        $referenceType = ($absolute) ? UrlGeneratorInterface::ABSOLUTE_URL : UrlGeneratorInterface::ABSOLUTE_PATH;        
        $url           = $this->router->generate($route, $routeParams, $referenceType);
        
        return $url.((!empty($clickthrough)) ? '?ct='.$this->encodeArrayForUrl($clickthrough) : '');
    }
}
