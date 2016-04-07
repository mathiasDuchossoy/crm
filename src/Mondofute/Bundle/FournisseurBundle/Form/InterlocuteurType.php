<?php

namespace Mondofute\Bundle\FournisseurBundle\Form;

use Mondofute\Bundle\FournisseurBundle\Entity\InterlocuteurFonction;
use Mondofute\Bundle\FournisseurBundle\Entity\ServiceInterlocuteur;
use Mondofute\Bundle\FournisseurBundle\Repository\InterlocuteurFonctionRepository;
use Mondofute\Bundle\FournisseurBundle\Repository\ServiceInterlocuteurRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InterlocuteurType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $locale = $options['locale'];
        $builder
            ->add('prenom')
            ->add('nom')
            ->add('fonction', EntityType::class, array(
                    'class' => InterlocuteurFonction::class,
                    'placeholder' => '--- choisir une fonction ---',
//                    'required' => false,
                    'choice_label' => 'traductions[0].libelle',
                    'query_builder' => function (InterlocuteurFonctionRepository $r) use ($locale) {
                        return $r->getTraductionsByLocale($locale);
                    },
                )
            )
            ->add('service', EntityType::class, array(
                    'class' => ServiceInterlocuteur::class,
                    'placeholder' => '--- choisir un service ---',
//                    'required' => false,
                    'choice_label' => 'traductions[0].libelle',
                    'query_builder' => function (ServiceInterlocuteurRepository $r) use ($locale) {
                        return $r->getTraductionsByLocale($locale);
                    },
                )
            )
            ->add('moyenComs',
                'Infinite\FormBundle\Form\Type\PolyCollectionType',
//                'Infinite\FormBundle\Form\Type\PolyCollectionType' ,
                array('types' => array(
//                    'Nucleus\MoyenComBundle\Form\AdresseType'
                    'nucleus_moyencombundle_adresse',
                    'nucleus_moyencombundle_email',
                    'nucleus_moyencombundle_fixe',
                    'nucleus_moyencombundle_mobile',
                ),
                    'prototype_name' => '__mycom_name__',
                    'allow_add' => true,
                )
            )
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mondofute\Bundle\FournisseurBundle\Entity\Interlocuteur',
            'locale' => 'fr_FR'
        ));
    }

}
