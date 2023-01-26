<?php

namespace MauticPlugin\MauticMultiDomainBundle\Controller\Api;

use Mautic\ApiBundle\Controller\CommonApiController;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use MauticPlugin\MauticMultiDomainBundle\Entity\Multidomain;

/**
 * Class MultidomainApiController.
 */
class MultidomainApiController extends CommonApiController
{
    /**
     * {@inheritdoc}
     */
    public function initialize(FilterControllerEvent $event)
    {
        $this->model            = $this->getModel('multidomain');
        $this->entityClass      = Multidomain::class;
        $this->entityNameOne    = 'multidomain';
        $this->entityNameMulti  = 'multidomain';
        $this->serializerGroups = ['multidomainDetails', 'publishDetails'];

        parent::initialize($event);
    }
}