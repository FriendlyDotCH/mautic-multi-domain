<?php

namespace MauticPlugin\MauticMultiDomainBundle\Security\Permissions;

use Mautic\CoreBundle\Security\Permissions\AbstractPermissions;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class MauticFocusPermissions.
 */
class MultidomainPermissions extends AbstractPermissions
{
    /**
     * @param array<string, mixed> $params
     */
    public function __construct(array $params)
    {
        parent::__construct($params);
        $this->addStandardPermissions(['categories']);
        $this->addExtendedPermissions('items');
    }

    /**
     * {@inheritdoc}
     *
     * @return string|void
     */
    public function getName()
    {
        return 'multiDomain';
    }

    /**
     * @param array<string, mixed> $options
     * @param array<string, mixed> $data
     */
    public function buildForm(FormBuilderInterface &$builder, array $options, array $data): void
    {
        $this->addStandardFormFields('multiDomain', 'categories', $builder, $data);
        $this->addExtendedFormFields('multiDomain', 'items', $builder, $data);
    }
}
