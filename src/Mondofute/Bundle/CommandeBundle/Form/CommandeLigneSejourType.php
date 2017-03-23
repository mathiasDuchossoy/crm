<?php

namespace Mondofute\Bundle\CommandeBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommandeLigneSejourType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
//            ->add('montant')
            ->add('_type', HiddenType::class, array(
                'data' => 'sejour', // Arbitrary, but must be distinct
                'mapped' => false
            ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mondofute\Bundle\CommandeBundle\Entity\CommandeLigneSejour',
            'model_class' => 'Mondofute\Bundle\CommandeBundle\Entity\CommandeLigneSejour',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'mondofute_bundle_commandebundle_commandelignesejour';
    }


}
