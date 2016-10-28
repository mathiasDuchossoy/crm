<?php

namespace Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Form;

use Mondofute\Bundle\FournisseurPrestationAffectationBundle\Entity\ModeAffectation;
use Mondofute\Bundle\FournisseurPrestationAffectationBundle\Form\PrestationAnnexeFournisseurType;
use Mondofute\Bundle\FournisseurPrestationAffectationBundle\Form\PrestationAnnexeHebergementType;
use Mondofute\Bundle\FournisseurPrestationAffectationBundle\Form\PrestationAnnexeStationType;
use Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity\Type;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FournisseurPrestationAnnexeParamType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type', ChoiceType::class, array(
                'choices' => array(
                    Type::getLibelle(Type::Individuelle) => Type::Individuelle,
                    Type::getLibelle(Type::Quantite) => Type::Quantite,
                    Type::getLibelle(Type::Forfait) => Type::Forfait,
                ),
                "placeholder" => " --- choisir un type ---",
                'choices_as_values' => true,
                'label' => 'type',
                'translation_domain' => 'messages',
                'required' => true,
            ))
            ->add('capacite', FournisseurPrestationAnnexeCapaciteType::class, array('required' => false,))
            ->add('dureeSejour', FournisseurPrestationAnnexeDureeSejourType::class, array('required' => false,))
            ->add('traductions', CollectionType::class, array(
                'entry_type' => FournisseurPrestationAnnexeParamTraductionType::class,
                'allow_add' => true,
            ))
            ->add('tarifs', CollectionType::class, array(
                'entry_type' => PrestationAnnexeTarifType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'translation_domain' => 'messages',
                'prototype_name'    => '__name_tarif__'
            ))
            ->add('modeAffectation', ChoiceType::class, array(
                'choices' => array(
                    ModeAffectation::getLibelle(ModeAffectation::Station) => ModeAffectation::Station,
                    ModeAffectation::getLibelle(ModeAffectation::Fournisseur) => ModeAffectation::Fournisseur
                ),
                'choices_as_values' => true,
                'expanded' => true,
                'required' => true,
                'attr'      => array(
                    'onchange'   => 'chargerAffectations(this)',
                    'class'     => 'form-inline'
                ),
            ))
            ->add('prestationAnnexeFournisseurs', CollectionType::class, array(
                'entry_type' => PrestationAnnexeFournisseurType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'translation_domain' => 'messages',
            ))
            ->add('prestationAnnexeHebergements', CollectionType::class, array(
                'entry_type' => PrestationAnnexeHebergementType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'translation_domain' => 'messages',
            ))
            ->add('prestationAnnexeStations', CollectionType::class, array(
                'entry_type' => PrestationAnnexeStationType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'translation_domain' => 'messages',
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity\FournisseurPrestationAnnexeParam'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'mondofute_bundle_fournisseurprestationannexebundle_fournisseurprestationannexeparam';
    }


}
