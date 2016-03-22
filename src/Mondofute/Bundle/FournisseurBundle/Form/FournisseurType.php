<?php

namespace Mondofute\Bundle\FournisseurBundle\Form;

use Mondofute\Bundle\FournisseurBundle\Entity\Fournisseur;
use Mondofute\Bundle\FournisseurBundle\Entity\FournisseurContient;
use Mondofute\Bundle\FournisseurBundle\Repository\FournisseurRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FournisseurType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $fournisseurId = $builder->getData()->getId();
        $builder
            ->add('enseigne')
            ->add('type', EntityType::class, array(
                'choice_label' => 'libelle',
                'class' => 'Mondofute\Bundle\FournisseurBundle\Entity\TypeFournisseur',
                'placeholder' => ' ----- Choisir un type de fournisseur ----- '
            ))
            ->add('fournisseurParent', EntityType::class, array(
                'choice_label' => 'enseigne',
                'class' => 'Mondofute\Bundle\FournisseurBundle\Entity\Fournisseur',
                'placeholder' => ' ----- Choisir un fournisseur parent ----- ',
                'required' => false,
                'query_builder' => function (FournisseurRepository $r) use ($fournisseurId) {
                    return $r->getFournisseurDeFournisseur($fournisseurId);
                },
            ))
            ->add('contient', ChoiceType::class, array(
                'choices' => array(
                    FournisseurContient::getLibelle(FournisseurContient::FOURNISSEUR) => FournisseurContient::FOURNISSEUR,
                    FournisseurContient::getLibelle(FournisseurContient::PRODUIT) => FournisseurContient::PRODUIT
                ),
                'choices_as_values' => true
            ))
            ->add('interlocuteurs', CollectionType::class
                , array(
                    'entry_type' => 'Mondofute\Bundle\FournisseurBundle\Form\FournisseurInterlocuteurType',
                    'allow_add' => true,
                    'allow_delete' => true,
                )
            );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mondofute\Bundle\FournisseurBundle\Entity\Fournisseur'
        ));
    }
}
