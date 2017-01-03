<?php

namespace Mondofute\Bundle\PromotionBundle\Form;

use Mondofute\Bundle\FournisseurBundle\Entity\Fournisseur;
use Mondofute\Bundle\StationBundle\Entity\Station;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PromotionStationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('station', EntityType::class, array(
                'class' => Station::class,
                'property' => 'id'
            ))
            ->add('fournisseur', EntityType::class, array(
                'class' => Fournisseur::class,
                'property' => 'id'
            ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mondofute\Bundle\PromotionBundle\Entity\PromotionStation'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'mondofute_bundle_promotionbundle_promotionstation';
    }


}
