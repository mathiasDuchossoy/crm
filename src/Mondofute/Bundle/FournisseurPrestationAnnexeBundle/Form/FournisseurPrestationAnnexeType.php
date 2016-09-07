<?php

namespace Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FournisseurPrestationAnnexeType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('libelle')
            ->add('type')
            ->add('capacite')
            ->add('dureeSejour')
            ->add('fournisseur')
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity\FournisseurPrestationAnnexe'
        ));
    }
}
