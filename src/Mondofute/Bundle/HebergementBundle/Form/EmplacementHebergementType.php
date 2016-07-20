<?php

namespace Mondofute\Bundle\HebergementBundle\Form;

use Mondofute\Bundle\HebergementBundle\Entity\Emplacement;
use Mondofute\Bundle\UniteBundle\Form\DistanceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EmplacementHebergementType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('checkbox', CheckboxType::class, array('mapped' => false, 'required' => false, 'label' => 'activer'))
            ->add('distance1', DistanceType::class, array('required' => false))
            ->add('distance2', DistanceType::class, array('required' => false))
            ->add('typeEmplacement', EntityType::class,
                array(
                    'class' => Emplacement::class,
//                    'property' => 'id',
                    'choice_label' => 'id',
                    'mapped' => false
                ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mondofute\Bundle\HebergementBundle\Entity\EmplacementHebergement'
        ));
    }
}
