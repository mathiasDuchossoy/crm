<?php

namespace Mondofute\Bundle\GeographieBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ZoneTouristiqueType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('traductions', CollectionType::class, array(
                'entry_type' => ZoneTouristiqueTraductionType::class,
            ))
            ->add('site', HiddenType::class, array('mapped' => false))
            ->add('images', CollectionType::class, array(
                'entry_type' => ZoneTouristiqueImageType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
//                'required' => false,
            ))
            ->add('photos', CollectionType::class, array(
                'entry_type' => ZoneTouristiquePhotoType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
//                'required' => false,
            ))
            ->add('videos', CollectionType::class, array(
                'entry_type' => ZoneTouristiqueVideoType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
//                'required' => false,
            ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mondofute\Bundle\GeographieBundle\Entity\ZoneTouristique'
        ));
    }
}
