<?php

namespace Mondofute\Bundle\TrancheHoraireBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TrancheHoraireType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('debut', TimeType::class, array('label' => 'debut', 'translation_domain' => 'messages'))
            ->add('fin', TimeType::class, array('label' => 'fin', 'translation_domain' => 'messages'));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mondofute\Bundle\TrancheHoraireBundle\Entity\TrancheHoraire'
        ));
    }
}
