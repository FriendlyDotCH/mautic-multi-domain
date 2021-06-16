<?php

/*
 * @copyright   2016 Mautic, Inc. All rights reserved
 * @author      Mautic, Inc
 *
 * @link        https://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

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
    public function setMultidomain(Multidomain $multidomain)
    {
        $this->entity = $multidomain;
    }
}
