<?php

namespace Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Form;

use Mondofute\Bundle\FournisseurPrestationAffectationBundle\Entity\ModeAffectation;
use Mondofute\Bundle\FournisseurPrestationAffectationBundle\Form\PrestationAnnexeFournisseurType;
use Mondofute\Bundle\FournisseurPrestationAffectationBundle\Form\PrestationAnnexeHebergementType;
use Mondofute\Bundle\FournisseurPrestationAffectationBundle\Form\PrestationAnnexeStationType;
use Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity\Type;
use Mondofute\Bundle\PrestationAnnexeBundle\Entity\PrestationAnnexe;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
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
        ;
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
