<?php

namespace Mondofute\Bundle\StationBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StationCommentVenirUnifieType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('stationCommentVenirs', CollectionType::class, array(
//                'auto_initialize' => false,
                'entry_type' => StationCommentVenirType::class,
                'entry_options' => array('locale' => $options["locale"],

                )
            ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mondofute\Bundle\StationBundle\Entity\StationCommentVenirUnifie',
            'locale' => 'fr_FR'
        ));
    }
}
