<?php

namespace Mondofute\Bundle\GeographieBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StationUnifieType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $firstZoneTouristique = $builder->getData()->getStations()->First()->getZoneTouristique();
        $siteZoneTouristique = (!empty($firstZoneTouristique)) ? $firstZoneTouristique->getSite() : null;
        $builder
            ->add('stations', CollectionType::class, array('entry_type' => StationType::class, 'entry_options' => array('locale' => $options["locale"], 'siteZoneTouristique' => $siteZoneTouristique)))
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mondofute\Bundle\GeographieBundle\Entity\StationUnifie',
            'locale' => 'fr_FR'
        ));
    }
}
