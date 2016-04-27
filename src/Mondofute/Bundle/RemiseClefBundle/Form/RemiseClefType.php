<?php

namespace Mondofute\Bundle\RemiseClefBundle\Form;

use Mondofute\Bundle\FournisseurBundle\Entity\Fournisseur;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RemiseClefType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fournisseur', EntityType::class, array('class' => Fournisseur::class, 'property' => 'id'))
            ->add('libelle')
            ->add('heureRemiseClefLongSejour')
            ->add('heureRemiseClefCourtSejour')
            ->add('heureDepartLongSejour')
            ->add('heureDepartCourtSejour')
            ->add('heureTardiveLongSejour')
            ->add('heureTardiveCourtSejour')
            ->add('standard')
            ->add('traductions', CollectionType::class, array('entry_type' => RemiseClefTraductionType::class));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mondofute\Bundle\RemiseClefBundle\Entity\RemiseClef'
        ));
    }
}
