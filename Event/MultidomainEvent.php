<?php

namespace MauticPlugin\MauticMultiDomainBundle\Event;

use Mautic\CoreBundle\Event\CommonEvent;
use MauticPlugin\MauticMultiDomainBundle\Entity\Multidomain;

/**
 * Class MultidomainEvent.
 */
class MultidomainEvent extends CommonEvent
{
    /**
     * @param bool|false $isNew
     */
    public function __construct(Multidomain $multidomain, $isNew = false)
    {
        $this->entity = $multidomain;
        $this->isNew  = $isNew;
    }

    /**
     * Returns the Multidomain entity.
     *
     * @return MultidomainEvent
     */
    public function getMultidomain()
    {
        return $this->entity;
    }

    /**
     * Sets the Multidomain entity.
     */
    public function setMultidomain(Multidomain $multidomain): void
    {
        $this->entity = $multidomain;
    }
}
