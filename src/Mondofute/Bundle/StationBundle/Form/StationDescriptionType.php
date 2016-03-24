<?php

namespace Mondofute\Bundle\StationBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StationDescriptionType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
//            ->add('stationDescriptionUnifie')
            ->add('traductions', CollectionType::class, array(
                'entry_type' => StationDescriptionTraductionType::class,
            ))
            ->add('site', HiddenType::class, array('mapped' => false));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mondofute\Bundle\StationBundle\Entity\StationDescription',
            'locale' => 'fr_FR'
        ));
    }
}
