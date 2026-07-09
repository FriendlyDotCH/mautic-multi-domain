<?php

namespace MauticPlugin\MauticMultiDomainBundle\Controller\Api;

use Mautic\ApiBundle\Controller\CommonApiController;
use MauticPlugin\MauticMultiDomainBundle\Entity\Multidomain;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

/**
 * Class MultidomainApiController.
 */
class MultidomainApiController extends CommonApiController
{
    /**
     * {@inheritdoc}
     */
    public function initialize(FilterControllerEvent $event): void
    {
        $this->model            = $this->getModel('multidomain');
        $this->entityClass      = Multidomain::class;
        $this->entityNameOne    = 'multidomain';
        $this->entityNameMulti  = 'multidomain';
        $this->serializerGroups = ['multidomainDetails', 'publishDetails'];
    }
}
