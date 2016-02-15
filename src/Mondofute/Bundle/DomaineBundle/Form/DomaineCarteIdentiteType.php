<?php

namespace Mondofute\Bundle\DomaineBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DomaineCarteIdentiteType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('altitudeMini', null, array('attr' => array('min' => 0)))
            ->add('altitudeMaxi', null, array('attr' => array('min' => 0)))
            ->add('kmPistesSkiAlpin', null, array('attr' => array('min' => 0)))
            ->add('kmPistesSkiNordique', null, array('attr' => array('min' => 0)))
            ->add('traductions', CollectionType::class, array(
                'entry_type' => DomaineCarteIdentiteTraductionType::class
            ))
            ->add('site', HiddenType::class, array('mapped' => false))
            ->add('snowpark', SnowparkType::class)
//            ->add('snowpark', SnowparkType::class, array('required' => false,))
            ->add('handiski', HandiskiType::class, array('required' => false,));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mondofute\Bundle\DomaineBundle\Entity\DomaineCarteIdentite'
        ));
    }
}
