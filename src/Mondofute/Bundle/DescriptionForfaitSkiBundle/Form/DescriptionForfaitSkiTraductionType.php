<?php

namespace Mondofute\Bundle\DescriptionForfaitSkiBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DescriptionForfaitSkiTraductionType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('libelle', null, array('mapped' => false))
            ->add('texteDur')
            ->add('description', TextareaType::class, array('required' => false))
            ->add('langue', HiddenType::class, array('mapped' => false));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mondofute\Bundle\DescriptionForfaitSkiBundle\Entity\DescriptionForfaitSkiTraduction'
        ));
    }
}
