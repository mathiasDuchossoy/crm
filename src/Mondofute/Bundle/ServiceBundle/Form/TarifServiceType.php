<?php

namespace Mondofute\Bundle\ServiceBundle\Form;

use Mondofute\Bundle\PeriodeBundle\Entity\TypePeriode;
use Mondofute\Bundle\UniteBundle\Form\TarifType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

//use Mondofute\Bundle\UniteBundle\Entity\UnitePeriode;
//use Mondofute\Bundle\UniteBundle\Form\UnitePeriodeType;

class TarifServiceType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('tarif', TarifType::class)
            ->add('typePeriode', EntityType::class, array(
                'class' => TypePeriode::class,
                'choice_translation_domain' => 'messages',
                'translation_domain' => 'messages',
//                'choice_label' => 'id',
            ))
            ->add('service');
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mondofute\Bundle\ServiceBundle\Entity\TarifService'
        ));
    }
}
