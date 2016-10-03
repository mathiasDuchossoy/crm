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
//                'expanded' => true,
                'required' => true,
            ))
            ->add('capacite', FournisseurPrestationAnnexeCapaciteType::class, array('required' => false,))
            ->add('dureeSejour', FournisseurPrestationAnnexeDureeSejourType::class, array('required' => false,))
            ->add('traductions', CollectionType::class, array(
                'entry_type' => FournisseurPrestationAnnexeTraductionType::class,
                'allow_add' => true,
            ))
//            ->add('prestationAnnexe', EntityType::class, array(
//                'class' => PrestationAnnexe::class,
//                'required' => true,
//                "choice_label" => "id",
//            ))
            ->add('prestationAnnexe', EntityType::class, array(
                'class' => PrestationAnnexe::class,
                'choice_label' => 'id',
                'label_attr' => [
                    'style' => 'display:none',
                ],
                'attr' => [
                    'style' => 'display:none',
                ],
            ))
            ->add('tarifs', CollectionType::class, array(
                'entry_type' => PrestationAnnexeTarifType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
//                'label' => 'prestation.annexe',
                'translation_domain' => 'messages',
//                'prototype_name' => '__tarif_name__',
//                'options'   => array(
//                    'famillePrestationAnnexeId' => $options['famillePrestationAnnexeId']
//                )
            ))
            ->add('modeAffectation', ChoiceType::class, array(
                'choices' => array(
                    ModeAffectation::getLibelle(ModeAffectation::Station) => ModeAffectation::Station,
                    ModeAffectation::getLibelle(ModeAffectation::Fournisseur) => ModeAffectation::Fournisseur
                ),
//                "placeholder" => " --- choisir un type ---",
                'choices_as_values' => true,
//                'label' => 'type',
//                'translation_domain' => 'messages',
                'expanded' => true,
                'required' => true,
                'attr'      => array(
                    'onchange'   => 'chargerAffectations(this)',
                    'class'     => 'form-inline'
                ),
//                'preferred_choices' => array(ModeAffectation::Station)
//                'data'      => ModeAffectation::Station
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
