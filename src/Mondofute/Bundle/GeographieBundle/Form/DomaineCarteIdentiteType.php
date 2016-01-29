<?php

namespace Mondofute\Bundle\GeographieBundle\Form;

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
            ->add('altitudeMini', null, array('required' => false))
            ->add('altitudeMaxi', null, array('required' => false))
            ->add('kmPistesSkiAlpin', null, array('required' => false))
            ->add('kmPistesSkiNordique', null, array('required' => false))
            ->add('traductions', CollectionType::class, array(
                'entry_type' => DomaineCarteIdentiteTraductionType::class,
                'required' => false,
            ))
            ->add('site', HiddenType::class, array('mapped' => false));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mondofute\Bundle\GeographieBundle\Entity\DomaineCarteIdentite'
        ));
    }
}
