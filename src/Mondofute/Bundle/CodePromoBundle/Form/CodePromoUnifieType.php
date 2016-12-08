<?php

namespace Mondofute\Bundle\CodePromoBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CodePromoUnifieType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
//        dump($builder->getData());die;
        $builder
            ->add('codePromos', CollectionType::class, array(
                'entry_type' => CodePromoType::class,
                'options' => array('clients' => $options['clients']),
//                'cascade_validation' => true
            ))
            ->add('code');
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mondofute\Bundle\CodePromoBundle\Entity\CodePromoUnifie',
            'clients' => array(),
//            'validation_groups' => array('Default' , 'registration')
        ));
    }
}
