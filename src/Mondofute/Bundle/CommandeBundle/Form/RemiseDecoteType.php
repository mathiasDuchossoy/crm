<?php

namespace Mondofute\Bundle\CommandeBundle\Form;

use Mondofute\Bundle\DecoteBundle\Entity\Decote;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RemiseDecoteType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $site = $options['site'];
        $builder
            ->add('prixVente')
            ->add('decote', EntityType::class, [
                'class' => Decote::class,
                'choice_label' => 'libelle',
                'attr' => [
                    'class' => 'js-decote',
                    'data-site' => $site->getId()
                ],
                'empty_value' => ' --- Rechercher une decote ---'
            ])
            ->add('_type', HiddenType::class, array(
                'data' => 'remiseDecote', // Arbitrary, but must be distinct
                'mapped' => false
            ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mondofute\Bundle\CommandeBundle\Entity\RemiseDecote',
            'model_class' => 'Mondofute\Bundle\CommandeBundle\Entity\RemiseDecote',
            'site' => null
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'mondofute_bundle_commandebundle_remisedecote';
    }


}
