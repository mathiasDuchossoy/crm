<?php

namespace Mondofute\Bundle\FournisseurBundle\Form;

use Mondofute\Bundle\TrancheHoraireBundle\Form\TrancheHoraireType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReceptionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('jour', null, array('label' => 'jour', 'translation_domain' => 'messages'))
            ->add('tranche1', TrancheHoraireType::class,
                array('label' => 'tranche1', 'translation_domain' => 'messages'))
            ->add('tranche2', TrancheHoraireType::class,
                array('label' => 'tranche2', 'translation_domain' => 'messages'))
//            ->add('fournisseur', EntityType::class, array(
//                'class' => Fournisseur::class,
//                'choice_label' => 'id'
//            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mondofute\Bundle\HebergementBundle\Entity\Reception',
        ));
    }

    public function getName()
    {
        return 'mondofute_fournisseur_bundle_reception_type';
    }

    public function getBlockPrefix()
    {
        return 'mondofute_fournisseur_bundle_reception_type';
    }
}
