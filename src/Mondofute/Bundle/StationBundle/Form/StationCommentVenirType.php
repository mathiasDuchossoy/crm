<?php

namespace Mondofute\Bundle\StationBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StationCommentVenirType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $locale = $options["locale"];
//        echo $locale = $options["locale"];
//        die;
        $builder
            ->add('site', HiddenType::class, array('mapped' => false))
            ->add('traductions', CollectionType::class, array(
                'entry_type' => StationCommentVenirTraductionType::class
            ))
            ->add('grandeVilles', CollectionType::class, array(
                'entry_type' => StationCommentVenirGrandeVilleType::class,
                'entry_options' => array('locale' => $locale),
                'required' => false,
//                'label_attr' => array('style' => 'display:none')
            ))//            ->add('stationCommentVenirUnifie')
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mondofute\Bundle\StationBundle\Entity\StationCommentVenir',
            'locale' => 'fr_FR'
        ));
    }
}
