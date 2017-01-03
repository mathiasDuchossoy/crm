<?php

namespace Mondofute\Bundle\DecoteBundle\Form;

use Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity\FournisseurPrestationAnnexe;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DecoteFournisseurPrestationAnnexeType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fournisseurPrestationAnnexe', EntityType::class, array(
                'class' => FournisseurPrestationAnnexe::class,
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
            'data_class' => 'Mondofute\Bundle\DecoteBundle\Entity\DecoteFournisseurPrestationAnnexe'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'mondofute_bundle_decotebundle_decotefournisseurprestationannexe';
    }


}
