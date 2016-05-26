<?php

namespace Mondofute\Bundle\FournisseurBundle\Form;

use Mondofute\Bundle\FournisseurBundle\Entity\TypeFournisseur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TypeFournisseurType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
//            ->add('typeFournisseur')
            ->add('fournisseur')
            ->add('typeFournisseurs', ChoiceType::class, array(
                'choices' => array(
                    TypeFournisseur::getLibelle(TypeFournisseur::Hebergement) => TypeFournisseur::Hebergement,
                    TypeFournisseur::getLibelle(TypeFournisseur::RemonteesMecaniques) => TypeFournisseur::RemonteesMecaniques,
                    TypeFournisseur::getLibelle(TypeFournisseur::LocationMaterielDeSki) => TypeFournisseur::LocationMaterielDeSki,
                    TypeFournisseur::getLibelle(TypeFournisseur::ESF) => TypeFournisseur::ESF,
                    TypeFournisseur::getLibelle(TypeFournisseur::Assurance) => TypeFournisseur::Assurance,
                ),
                'choices_as_values' => true,
                'label' => 'typeFournisseur',
                'translation_domain' => 'messages',
            ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mondofute\Bundle\FournisseurBundle\Entity\TypeFournisseur'
        ));
    }
}
