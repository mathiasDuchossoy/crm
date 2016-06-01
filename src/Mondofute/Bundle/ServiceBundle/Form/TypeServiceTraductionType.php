<?php

namespace Mondofute\Bundle\ServiceBundle\Form;

use Mondofute\Bundle\ServiceBundle\Entity\TypeServiceTraduction;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TypeServiceTraductionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('libelle');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => TypeServiceTraduction::class
        ));
    }

    public function getName()
    {
        return 'mondofute_service_bundle_type_service_traduction_type';
    }
}
