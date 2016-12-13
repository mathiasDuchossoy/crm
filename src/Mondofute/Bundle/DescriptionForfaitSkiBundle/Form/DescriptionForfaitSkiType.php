<?php

namespace Mondofute\Bundle\DescriptionForfaitSkiBundle\Form;

use Mondofute\Bundle\ChoixBundle\Entity\OuiNonNC;
use Mondofute\Bundle\ChoixBundle\Repository\OuiNonNCRepository;
use Mondofute\Bundle\UniteBundle\Form\AgeType;
use Mondofute\Bundle\UniteBundle\Form\TarifType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DescriptionForfaitSkiType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $locale = $options["locale"];
        $builder
            ->add('quantite')
            ->add('classement')
            ->add('prix', 'Mondofute\Bundle\UniteBundle\Form\TarifType', array('required' => false))
            ->add('ageMin', 'Mondofute\Bundle\UniteBundle\Form\AgeType', array('required' => false))
            ->add('ageMax', 'Mondofute\Bundle\UniteBundle\Form\AgeType', array('required' => false))
            ->add('present',
                EntityType::class,
                array(
                    'class' => OuiNonNC::class,
//                    'placeholder' => '--- Veuillez choisir une unité ---',
//                    ''
                    'choice_label' => 'traductions[0].libelle',
                    'query_builder' => function (OuiNonNCRepository $r) use ($locale) {
                        return $r->getTraductionsByLocale($locale);
                    },
                )
            )
            ->add('traductions', CollectionType::class, array(
                'entry_type' => DescriptionForfaitSkiTraductionType::class,
            ))//            ->add('ligneDescriptionForfaitSki', HiddenType::class, array('mapped' => false))
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mondofute\Bundle\DescriptionForfaitSkiBundle\Entity\DescriptionForfaitSki',
            'locale' => 'fr_FR',
        ));
    }
}
