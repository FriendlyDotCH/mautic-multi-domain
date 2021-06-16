<?php

namespace MauticPlugin\MauticMultiDomainBundle\Entity;

use Mautic\CoreBundle\Entity\CommonRepository;

/**
 * MultidomainRepository
 */
class MultidomainRepository extends CommonRepository
{
    public function getByPublished($isPublished = true)
    {
        $q = $this->createQueryBuilder('f');
        $q->select('md')
        ->from(Multidomain::class, 'md')
        ->where('md.isPublished = :isPublished')
        ->setParameters(['isPublished' => $isPublished])
        ;
        return $q->getQuery()->getResult();
    }
}