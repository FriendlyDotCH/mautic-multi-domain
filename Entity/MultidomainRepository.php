<?php

namespace MauticPlugin\MauticMultiDomainBundle\Entity;

use Mautic\CoreBundle\Entity\CommonRepository;

/**
 * MultidomainRepository.
 *
 * @extends CommonRepository<Multidomain>
 */
class MultidomainRepository extends CommonRepository
{
    /**
     * @return Multidomain[]
     */
    public function getByPublished(bool $isPublished = true): array
    {
        $q = $this->createQueryBuilder('f');
        $q->select('md')
        ->from(Multidomain::class, 'md')
        ->where('md.isPublished = :isPublished')
        ->setParameters(['isPublished' => $isPublished]);

        return $q->getQuery()->getResult();
    }
}
