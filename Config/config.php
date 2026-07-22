<?php

return [
    'name'        => 'Friendly Multi Domain',
    'description' => 'User can add multiple tracking domains for emails.',
    'author'      => 'Abdullah Kiser / Friendly Automate',
    'version'     => '7.0.1',
    'routes'      => [
        'main' => [
            'mautic_multidomain_index' => [
                'path'       => '/multidomain/{page}',
                'controller' => 'MauticPlugin\MauticMultiDomainBundle\Controller\MultidomainController:indexAction',
            ],
            'mautic_multidomain_action' => [
                'path'       => '/multidomain/{objectAction}/{objectId}',
                'controller' => 'MauticPlugin\MauticMultiDomainBundle\Controller\MultidomainController:executeAction',
            ],
        ],
        'api' => [
            'mautic_api_multidomainstandard' => [
                'standard_entity' => true,
                'name'            => 'multidomain',
                'path'            => '/multidomain',
                'controller'      => MauticPlugin\MauticMultiDomainBundle\Controller\Api\MultidomainApiController::class,
            ],
        ],
    ],
    'menu' => [
        'main' => [
            'mautic.multidomain.menu' => [
                'route'     => 'mautic_multidomain_index',
                'priority'  => 10,
                'iconClass' => 'fa-globe',
            ],
        ],
    ],
    'services' => [
        'forms' => [
            'mautic.form.type.multidomain' => [
                'class' => MauticPlugin\MauticMultiDomainBundle\Form\Type\MultidomainType::class,
            ],
        ],
        'models' => [],
        'events' => [
            'mautic.multidomain.subscriber.multidomain' => [
                'class'     => MauticPlugin\MauticMultiDomainBundle\EventListener\MultidomianSubscriber::class,
                'arguments' => [
                    'router',
                    'mautic.helper.ip_lookup',
                    'mautic.core.model.auditlog',
                    'mautic.page.model.trackable',
                    'mautic.page.helper.token',
                    'mautic.asset.helper.token',
                    'mautic.multidomain.model.multidomain',
                    'request_stack',
                ],
            ],
            'mautic.multidomain.subscriber.emailbuilder' => [
                'class'     => MauticPlugin\MauticMultiDomainBundle\EventListener\BuilderSubscriber::class,
                'arguments' => [
                    'mautic.helper.core_parameters',
                    'mautic.email.model.email',
                    'mautic.page.model.trackable',
                    'mautic.page.model.redirect',
                    'translator',
                    'doctrine.orm.entity_manager',
                    'mautic.multidomain.model.multidomain',
                    'router',
                ],
            ],
        ],
    ],
];
