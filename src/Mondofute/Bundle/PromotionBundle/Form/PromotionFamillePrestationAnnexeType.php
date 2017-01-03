<?php

namespace Mondofute\Bundle\PromotionBundle\Form;

use Mondofute\Bundle\PrestationAnnexeBundle\Entity\FamillePrestationAnnexe;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PromotionFamillePrestationAnnexeType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('famillePrestationAnnexe', EntityType::class, array(
                'class' => FamillePrestationAnnexe::class,
                'property' => 'id'
            ))
            ->add('fournisseur');
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mondofute\Bundle\PromotionBundle\Entity\PromotionFamillePrestationAnnexe'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'mondofute_bundle_promotionbundle_promotionfamilleprestationannexe';
    }


}
