<?php

namespace Mondofute\Bundle\CommandeBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommandeLigneFraisDossierType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('prixVente', null, [
                'attr' => [
                    'class' => 'prixVente',
                    'onchange' => 'calculPrixVenteTotal();'
                ]
            ])
            ->add('_type', HiddenType::class, array(
                'data' => 'fraisDossier', // Arbitrary, but must be distinct
                'mapped' => false
            ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mondofute\Bundle\CommandeBundle\Entity\CommandeLigneFraisDossier',
            'model_class' => 'Mondofute\Bundle\CommandeBundle\Entity\CommandeLigneFraisDossier'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'mondofute_bundle_commandebundle_commandelignefraisdossier';
    }


}
