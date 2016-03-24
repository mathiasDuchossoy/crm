<?php

namespace Mondofute\Bundle\UniteBundle\Form;

use Mondofute\Bundle\UniteBundle\Entity\UniteAge;
use Mondofute\Bundle\UniteBundle\Repository\UniteAgeRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AgeType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $locale = $options["locale"];
        $builder
            ->add('valeur')
            ->add('unite',
                EntityType::class,
                array(
                    'class' => UniteAge::class,
                    'placeholder' => '--- Veuillez choisir une unité ---',
                    'choice_label' => 'traductions[0].libelle',
                    'query_builder' => function (UniteAgeRepository $r) use ($locale) {
                        return $r->getTraductionsByLocale($locale);
                    },
                ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mondofute\Bundle\UniteBundle\Entity\Age',
            'locale' => 'fr_FR',
//            'locale' => 'es_ES',
        ));
    }
}
