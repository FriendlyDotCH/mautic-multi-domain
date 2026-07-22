<?php

namespace MauticPlugin\MauticMultiDomainBundle\Controller\Api;

use Doctrine\Persistence\ManagerRegistry;
use Mautic\ApiBundle\Controller\CommonApiController;
use Mautic\ApiBundle\Helper\EntityResultHelper;
use Mautic\CoreBundle\Factory\ModelFactory;
use Mautic\CoreBundle\Helper\AppVersion;
use Mautic\CoreBundle\Helper\CoreParametersHelper;
use Mautic\CoreBundle\Security\Permissions\CorePermissions;
use Mautic\CoreBundle\Translation\Translator;
use MauticPlugin\MauticMultiDomainBundle\Entity\Multidomain;
use MauticPlugin\MauticMultiDomainBundle\Model\MultidomainModel;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;

/**
 * @extends CommonApiController<Multidomain>
 */
class MultidomainApiController extends CommonApiController
{
    public function __construct(CorePermissions $security, Translator $translator, EntityResultHelper $entityResultHelper, RouterInterface $router, FormFactoryInterface $formFactory, AppVersion $appVersion, RequestStack $requestStack, ManagerRegistry $doctrine, ModelFactory $modelFactory, EventDispatcherInterface $dispatcher, CoreParametersHelper $coreParametersHelper)
    {
        $multidomainModel = $modelFactory->getModel('multidomain');
        \assert($multidomainModel instanceof MultidomainModel);

        $this->model            = $multidomainModel;
        $this->entityClass      = Multidomain::class;
        $this->entityNameOne    = 'multidomain';
        $this->entityNameMulti  = 'multidomain';
        $this->serializerGroups = ['multidomainDetails', 'publishDetails'];

        parent::__construct($security, $translator, $entityResultHelper, $router, $formFactory, $appVersion, $requestStack, $doctrine, $modelFactory, $dispatcher, $coreParametersHelper);
    }
}
