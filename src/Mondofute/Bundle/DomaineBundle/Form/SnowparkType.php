<?php

namespace Mondofute\Bundle\DomaineBundle\Form;

use Mondofute\Bundle\ChoixBundle\Entity\OuiNonNC;
use Mondofute\Bundle\ChoixBundle\Repository\OuiNonNCRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SnowparkType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $locale = $options["locale"];
        $builder
            ->add('present',
                EntityType::class,
                array(
                    'class' => OuiNonNC::class,
//                    'placeholder' => '--- Veuillez choisir une unité ---',
                    'choice_label' => 'traductions[0].libelle',
                    'query_builder' => function (OuiNonNCRepository $r) use ($locale) {
                        return $r->getTraductionsByLocale($locale);
                    },
                    'label' => 'Présence snowpark'
                ))
            ->add('traductions', CollectionType::class, array(
                'entry_type' => SnowparkTraductionType::class
            ));

    }


    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mondofute\Bundle\DomaineBundle\Entity\Snowpark',
            'locale' => 'fr_FR',
        ));
    }
}
