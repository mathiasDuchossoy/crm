<?php

namespace Mondofute\Bundle\ServiceBundle\Form;

use Mondofute\Bundle\PeriodeBundle\Entity\TypePeriode;
use Mondofute\Bundle\ServiceBundle\Entity\ServiceHebergementTarif;
use Mondofute\Bundle\UniteBundle\Form\TarifType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ServiceHebergementTarifType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('tarif', TarifType::class)
            ->add('typePeriode', EntityType::class, array(
                'class' => TypePeriode::class,
                'choice_translation_domain' => 'messages',
                'translation_domain' => 'messages',
            ))
//                'choice_label' => 'id',
//        ->add('service', EntityType::class,array('class'=>ServiceHebergement::class,'choice_label'=>'id'))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array('data_class' => ServiceHebergementTarif::class));
    }

    public function getBlockPrefix()
    {
        return 'mondofute_service_bundle_service_hebergement_tarif_type';
    }

    public function getName()
    {
        return 'mondofute_service_bundle_service_hebergement_tarif_type';
    }
}
