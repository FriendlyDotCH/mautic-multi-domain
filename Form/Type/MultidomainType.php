<?php

namespace MauticPlugin\MauticMultiDomainBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Mautic\CoreBundle\Form\Type\FormButtonsType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class MultidomainType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('email', TextType::class, [
                'label' => 'plugin.multidomain.email',
                'required' => true,
                'attr' => ['class' => 'form-control']
            ])
            ->add('domain', TextType::class, [
                'label' => 'plugin.multidomain.domain',
                'required' => true,
                'attr' => ['class' => 'form-control']
            ])
            ;
            
            $builder->add(
                'buttons',
                FormButtonsType::class
            );

        if (!empty($options['action'])) {
            $builder->setAction($options['action']);
        } 
    }
    
    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'multidomain_type';
    }
}