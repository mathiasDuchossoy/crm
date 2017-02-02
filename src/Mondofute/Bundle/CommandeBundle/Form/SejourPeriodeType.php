<?php

namespace Mondofute\Bundle\CommandeBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SejourPeriodeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('montant')
            ->add('commandeLignePrestationAnnexes', CollectionType::class, array(
                'entry_type' => CommandeLignePrestationAnnexeType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype_name' => '__name_commande_ligne_prestation_annexe__'
            ))
            ->add('_type', HiddenType::class, array(
                'data' => 'sejourPeriode', // Arbitrary, but must be distinct
                'mapped' => false
            ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mondofute\Bundle\CommandeBundle\Entity\SejourPeriode',
            'model_class' => 'Mondofute\Bundle\CommandeBundle\Entity\SejourPeriode'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'mondofute_bundle_commandebundle_sejourperiode';
    }


}
