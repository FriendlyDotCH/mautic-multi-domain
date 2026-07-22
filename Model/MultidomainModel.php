<?php

namespace MauticPlugin\MauticMultiDomainBundle\Model;

use Doctrine\ORM\EntityManagerInterface;
use Mautic\CoreBundle\Helper\CoreParametersHelper;
use Mautic\CoreBundle\Helper\UserHelper;
use Mautic\CoreBundle\Model\FormModel;
use Mautic\CoreBundle\Security\Permissions\CorePermissions;
use Mautic\CoreBundle\Translation\Translator;
use Mautic\LeadBundle\Model\FieldModel;
use Mautic\LeadBundle\Tracker\ContactTracker;
use Mautic\PageBundle\Model\TrackableModel;
use MauticPlugin\MauticMultiDomainBundle\Entity\Multidomain;
use MauticPlugin\MauticMultiDomainBundle\Event\MultidomainEvent;
use MauticPlugin\MauticMultiDomainBundle\Form\Type\MultidomainType;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * @extends FormModel<Multidomain>
 */
class MultidomainModel extends FormModel
{
    public function __construct(
        EntityManagerInterface $em,
        CorePermissions $security,
        EventDispatcherInterface $dispatcher,
        UrlGeneratorInterface $router,
        Translator $translator,
        UserHelper $userHelper,
        LoggerInterface $logger,
        CoreParametersHelper $coreParametersHelper,
        protected \Mautic\FormBundle\Model\FormModel $formModel,
        protected TrackableModel $trackableModel,
        protected FieldModel $leadFieldModel,
        protected ContactTracker $contactTracker,
    ) {
        parent::__construct(
            $em,
            $security,
            $dispatcher,
            $router,
            $translator,
            $userHelper,
            $logger,
            $coreParametersHelper
        );
    }

    /**
     * MultidomainModel constructor.
     */
    /*public function __construct(
    \Mautic\FormBundle\Model\FormModel $formModel,
    TrackableModel $trackableModel,
    EventDispatcherInterface $dispatcher,
    FieldModel $leadFieldModel,
    ContactTracker $contactTracker,
    EntityManager $entityManager
    ) {
    $this->formModel      = $formModel;
    $this->trackableModel = $trackableModel;
    // $this->templating     = $templating;
    $this->dispatcher     = $dispatcher;
    $this->leadFieldModel = $leadFieldModel;
    $this->contactTracker = $contactTracker;

    }*/

    /**
     * @return string
     */
    public function getActionRouteBase()
    {
        return 'multidomain';
    }

    /**
     * @return string
     */
    public function getPermissionBase()
    {
        return 'multidomain:items';
    }

    /**
     * {@inheritdoc}
     *
     * @param object                              $entity
     * @param \Symfony\Component\Form\FormFactory $formFactory
     * @param array<string, mixed>                $options
     *
     * @throws MethodNotAllowedHttpException
     */
    public function createForm($entity, $formFactory, $action = null, $options = []): \Symfony\Component\Form\FormInterface
    {
        if (!$entity instanceof Multidomain) {
            throw new MethodNotAllowedHttpException(['Multidomain']);
        }

        if (!empty($action)) {
            $options['action'] = $action;
        }

        return $formFactory->create(MultidomainType::class, $entity, $options);
    }

    /**
     * {@inheritdoc}
     *
     * @return \MauticPlugin\MauticMultiDomainBundle\Entity\MultidomainRepository
     */
    public function getRepository()
    {
        return $this->em->getRepository(Multidomain::class);
    }

    /**
     * {@inheritdoc}
     *
     * @return Multidomain
     */
    public function getEntity($id = null): ?object
    {
        if (null === $id) {
            return new Multidomain();
        }

        return parent::getEntity($id);
    }

    /**
     * {@inheritdoc}
     *
     * @param Multidomain $entity
     * @param bool|false  $unlock
     */
    public function saveEntity($entity, $unlock = true): void
    {
        parent::saveEntity($entity, $unlock);
        $this->getRepository()->saveEntity($entity);
    }

    public function generateMessageId(Multidomain $multidomain): string
    {
        $url   = $multidomain->getDomain();
        $parts = parse_url($url);
        if (!isset($parts['host'])) {
            throw new \Exception('InvalidDomainError');
        }

        $messageIdSuffix = '@'.$parts['host'];

        return bin2hex(random_bytes(16)).$messageIdSuffix;
    }

    /**
     * Get whether the color is light or dark.
     *
     * @return bool
     */
    public static function isLightColor(string $hex, int $level = 200)
    {
        $hex = str_replace('#', '', $hex);
        $r   = hexdec(substr($hex, 0, 2));
        $g   = hexdec(substr($hex, 2, 2));
        $b   = hexdec(substr($hex, 4, 2));

        $compareWith = ((($r * 299) + ($g * 587) + ($b * 114)) / 1000);

        return $compareWith >= $level;
    }

    /**
     * {@inheritdoc}
     *
     * @throws MethodNotAllowedHttpException
     */
    protected function dispatchEvent($action, &$entity, $isNew = false, ?Event $event = null): ?Event
    {
        if (!$entity instanceof Multidomain) {
            throw new MethodNotAllowedHttpException(['Multidomain']);
        }

        switch ($action) {
            case 'pre_save':
                $name = 'mautic.multidomain_pre_save';
                break;
            case 'post_save':
                $name = 'mautic.multidomain_post_save';
                break;
            case 'pre_delete':
                $name = 'mautic.multidomain_pre_delete';
                break;
            case 'post_delete':
                $name = 'mautic.multidomain_post_delete';
                break;
            default:
                return null;
        }

        if ($this->dispatcher->hasListeners($name)) {
            if (empty($event)) {
                $event = new MultidomainEvent($entity, $isNew);
                $event->setEntityManager($this->em);
            }

            $this->dispatcher->dispatch($event, $name);

            return $event;
        }

        return null;
    }

    /**
     * Get the content of the config.php file.
     *
     * @return array<string, mixed>
     */
    public function getConfiArray(): array
    {
        return include dirname(__DIR__).'/Config/config.php';
    }
}
