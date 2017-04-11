<?php

namespace Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Form;

use Mondofute\Bundle\SaisonBundle\Form\SaisonCodePasserelleType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FournisseurPrestationAnnexeType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('traductions', CollectionType::class, array(
                'entry_type' => FournisseurPrestationAnnexeTraductionType::class,
                'allow_add' => true,
            ))
            ->add('params', CollectionType::class, array(
                'entry_type' => FournisseurPrestationAnnexeParamType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype_name' => '__name_param__'
            ))
            ->add('freeSale')
            ->add('periodeIndisponibles', CollectionType::class, array(
                    'entry_type' => FournisseurPrestationAnnexePeriodeIndisponibleType::class,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => false,
                )
            )
            ->add('saisonCodePasserelles', CollectionType::class,
                [
                    'entry_type' => SaisonCodePasserelleType::class,
                ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity\FournisseurPrestationAnnexe',
            'famillePrestationAnnexeId' => null,
        ));
    }
}
