<?php

namespace Mondofute\Bundle\FournisseurPrestationAffectationBundle\Form;

use Mondofute\Bundle\FournisseurPrestationAffectationBundle\Entity\PrestationAnnexeStation;
use Mondofute\Bundle\StationBundle\Entity\Station;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PrestationAnnexeStationType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
//            ->add('prestationAnnexeStationUnifie')
            ->add('station', EntityType::class, array(
                'class' => Station::class,
                'property' => 'id'
            ))
            ->add('site', HiddenType::class, array('mapped' => false));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mondofute\Bundle\FournisseurPrestationAffectationBundle\Entity\PrestationAnnexeStation'
        ));
    }
}
