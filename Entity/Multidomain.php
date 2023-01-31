<?php

namespace MauticPlugin\MauticMultiDomainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Mautic\ApiBundle\Serializer\Driver\ApiMetadataDriver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Mautic\CategoryBundle\Entity\Category;
use Mautic\CoreBundle\Doctrine\Mapping\ClassMetadataBuilder;
use Mautic\CoreBundle\Entity\FormEntity;
use Mautic\FormBundle\Entity\Form;

/**
 * This class processes payment requests from Webpayment
 * Class Multidomain
 * @package MauticPlugin\MauticMultiDomainBundle\Entity
 */
class Multidomain extends FormEntity
{
    /**
     * @var int
     */
    private $id;

    /**
     * 
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $domain;

    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {

        $metadata->addConstraint(new UniqueEntity([
            'fields' => 'email',
        ]));
        $metadata->addPropertyConstraint(
            'email',
            new Assert\NotBlank(
                [
                    'message' => 'mautic.multidomain.email.required',
                ]
            )
        );

        $metadata->addPropertyConstraint(
            'email',
            new Assert\Email(
                [
                    'message' => 'mautic.multidomain.email.invalid',
                ]
            )
        );

        $metadata->addPropertyConstraint(
            'domain',
            new Assert\NotBlank(
                ['message' => 'mautic.multidomain.domain.required']
            )
        );

        $metadata->addPropertyConstraint(
            'domain',
            new Assert\Url(
                ['message' => 'mautic.multidomain.domain.invalid']
            )
        );
    }

    /**
     * @param ORM\ClassMetadata $metadata
     */
    public static function loadMetadata (ORM\ClassMetadata $metadata)
    {
        $builder = new ClassMetadataBuilder($metadata);

        $builder->setTable('multi_domain')
            ->setCustomRepositoryClass(MultidomainRepository::class);

        // Helper functions
        $builder->addId();
        
        $builder->createField('email', 'string')
            ->columnName('email')
            ->build();

        $builder->createField('domain', 'text')
            ->columnName('domain')
            ->build();
 
    }

    /**
     * Prepares the metadata for API usage.
     *
     * @param $metadata
     */
    public static function loadApiMetadata(ApiMetadataDriver $metadata)
    {
        $metadata->setGroupPrefix('multidomain')
            ->addListProperties(
                [
                    'id',
                    'email',
                    'domain',
                ]
            )
            ->build();
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): Multidomain
    {
        $this->email = $email;
        return $this;
    }

    public function getDomain(): ?string
    {
        return $this->domain;
    }

    public function setDomain(string $text): Multidomain
    {
        $this->domain = $text;
        return $this;
    }

    /**
     * Get Fake name to be compatable with getName of commonEntity. 
     */
    public function getName()
    {
        return $this->email;
    }

    /**
     * Set Fake name to be compatable with getName of commonEntity. 
     * 
     */
    public function setName(string $email): Multidomain
    {
        $this->email = $email;
        return $this;
    }
}