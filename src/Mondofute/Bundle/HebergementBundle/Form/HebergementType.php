<?php

namespace Mondofute\Bundle\HebergementBundle\Form;

use Mondofute\Bundle\StationBundle\Entity\Station;
use Mondofute\Bundle\StationBundle\Repository\StationRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HebergementType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $locale = $options["locale"];
        $builder
            ->add('traductions', CollectionType::class, array(
                'entry_type' => HebergementTraductionType::class,
            ))
            ->add('station', EntityType::class, array(
                'class' => Station::class,
                'placeholder' => '--- Veuillez choisir une station ---',
                'choice_label' => 'traductions[0].libelle',
                'query_builder' => function (StationRepository $st) use ($locale) {
                    return $st->getTraductionsByLocale($locale);
                },
            ))
            ->add('site', HiddenType::class, array('mapped' => false))
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mondofute\Bundle\HebergementBundle\Entity\Hebergement',
            'locale' => 'fr_FR'
        ));
    }
}
