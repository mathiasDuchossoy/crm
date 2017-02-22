<?php

namespace Mondofute\Bundle\CommandeBundle\Form;

use Mondofute\Bundle\PromotionBundle\Entity\Promotion;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RemisePromotionType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $site = $options['site'];
        $builder
            ->add('promotion', EntityType::class, [
                'class' => Promotion::class,
                'choice_label' => 'libelle',
                'attr' => [
                    'class' => 'js-promotion',
                    'data-site' => $site->getId()
                ],
                'empty_value' => ' --- Rechercher une promotion ---'
            ])
            ->add('_type', HiddenType::class, array(
                'data' => 'remisePromotion', // Arbitrary, but must be distinct
                'mapped' => false
            ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mondofute\Bundle\CommandeBundle\Entity\RemisePromotion',
            'model_class' => 'Mondofute\Bundle\CommandeBundle\Entity\RemisePromotion',
            'site' => null
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'mondofute_bundle_commandebundle_remisepromotion';
    }


}
