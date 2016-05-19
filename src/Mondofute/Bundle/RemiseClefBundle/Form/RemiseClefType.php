<?php

namespace Mondofute\Bundle\RemiseClefBundle\Form;

use Mondofute\Bundle\FournisseurBundle\Entity\Fournisseur;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RemiseClefType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $hours = array(8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20);
        $minutes = array(0, 30);
//        $optionsHoraires = array(
//            'widget' => 'choice',
//            'hours' => ,
//            'minutes' => ,
//        );
        $builder
            ->add('fournisseur', EntityType::class, array(
                'class' => Fournisseur::class,
                'choice_label' => 'id',
                'attr' => array('style' => 'display:none'),
                'label' => 'fournisseur',
                'translation_domain' => 'messages',
                'label_attr' => array('style' => 'display: none'),
                'mapped' => false,
            ))
            ->add('libelle')
            ->add('heureRemiseClefLongSejour', TimeType::class, array(
                'widget' => 'choice',
                'hours' => $hours,
                'minutes' => $minutes,
                'label' => 'heure.remise.clef.long.sejour',
                'translation_domain' => 'messages',
            ))
            ->add('heureRemiseClefCourtSejour', TimeType::class, array(
                'widget' => 'choice',
                'hours' => $hours,
                'minutes' => $minutes,
                'label' => 'heure.remise.clef.court.sejour',
                'translation_domain' => 'messages',
            ))
            ->add('heureDepartLongSejour', TimeType::class, array(
                'widget' => 'choice',
                'hours' => $hours,
                'minutes' => $minutes,
                'label' => 'heure.depart.long.sejour',
                'translation_domain' => 'messages',
            ))
            ->add('heureDepartCourtSejour', TimeType::class, array(
                'widget' => 'choice',
                'hours' => $hours,
                'minutes' => $minutes,
                'label' => 'heure.depart.court.sejour',
                'translation_domain' => 'messages',
            ))
            ->add('heureTardiveLongSejour', TimeType::class, array(
                'widget' => 'choice',
                'hours' => $hours,
                'minutes' => $minutes,
                'label' => 'heure.tardive.long.sejour',
                'translation_domain' => 'messages',
            ))
            ->add('heureTardiveCourtSejour', TimeType::class, array(
                'widget' => 'choice',
                'hours' => $hours,
                'minutes' => $minutes,
                'label' => 'heure.tardive.court.sejour',
                'translation_domain' => 'messages',
            ))
            ->add('standard', null, array('label' => 'standard', 'translation_domain' => 'messages'))
            ->add('traductions', CollectionType::class, array(
                'entry_type' => RemiseClefTraductionType::class,
                'allow_add' => true,
                'label' => 'traductions',
                'translation_domain' => 'messages',
                'prototype_name' => '__tradname__'
            ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mondofute\Bundle\RemiseClefBundle\Entity\RemiseClef'
        ));
    }
}
