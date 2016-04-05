<?php

namespace Mondofute\Bundle\HebergementBundle\Form;

use Mondofute\Bundle\FournisseurBundle\Entity\Fournisseur;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FournisseurHebergementType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
//            ->add('hebergement', EntityType::class, array('class' => HebergementUnifie::class, 'choice_label', 'id'))
//            ->add('fournisseur', EntityType::class, array('class' => Fournisseur::class, 'choice_label' => 'enseigne'))
//            ->add('fournisseur', HiddenType::class, array('mapped' => false))
//            ->add('hebergement', EntityType::class, array('class' => HebergementUnifie::class, 'choice_label' => 'id'))
            ->add('fournisseur', EntityType::class, array('class' => Fournisseur::class, 'choice_label' => 'enseigne'))
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mondofute\Bundle\HebergementBundle\Entity\FournisseurHebergement'
        ));
    }
}
