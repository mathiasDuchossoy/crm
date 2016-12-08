<?php

namespace Mondofute\Bundle\FournisseurBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FournisseurInterlocuteurType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $fournisseurId = $options['fournisseurId'];

        $builder
//            ->add('fournisseur')
            ->add('interlocuteur', 'Mondofute\Bundle\FournisseurBundle\Form\InterlocuteurType', array());
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mondofute\Bundle\FournisseurBundle\Entity\FournisseurInterlocuteur',
            'fournisseurId' => null,
        ));
    }
}
