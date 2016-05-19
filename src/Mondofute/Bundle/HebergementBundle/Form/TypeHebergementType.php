<?php

namespace Mondofute\Bundle\HebergementBundle\Form;

use Mondofute\Bundle\HebergementBundle\Entity\TypeHebergementTraduction;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TypeHebergementType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('individuel')
            ->add('site', HiddenType::class, array('mapped' => false))
            ->add('traductions', CollectionType::class, array(
                'entry_type' => TypeHebergementTraduction::class,
            ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mondofute\Bundle\HebergementBundle\Entity\TypeHebergement'
        ));
    }
}
