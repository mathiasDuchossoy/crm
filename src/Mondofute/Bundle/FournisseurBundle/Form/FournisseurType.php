<?php

namespace Mondofute\Bundle\FournisseurBundle\Form;

use Mondofute\Bundle\FournisseurBundle\Entity\Fournisseur;
use Mondofute\Bundle\FournisseurBundle\Entity\FournisseurContient;
use Mondofute\Bundle\FournisseurBundle\Repository\FournisseurRepository;
use Mondofute\Bundle\RemiseClefBundle\Form\RemiseClefType;
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
            ->add('raisonSociale')
            ->add('type', EntityType::class, array(
                'choice_label' => 'libelle',
                'class' => 'Mondofute\Bundle\FournisseurBundle\Entity\TypeFournisseur',
                'placeholder' => ' ----- Choisir un type de fournisseur ----- '
            ))
            ->add('enseigne', null, array('label' => 'enseigne', 'translation_domain' => 'messages'))
            ->add('fournisseurParent', EntityType::class, array(
                'choice_label' => 'enseigne',
                'class' => 'Mondofute\Bundle\FournisseurBundle\Entity\Fournisseur',
                'placeholder' => 'placeholder.choisir.fournisseur.parent',
                'required' => false,
                'label' => 'fournisseur.parent',
                'translation_domain' => 'messages',
                'query_builder' => function (FournisseurRepository $r) use ($fournisseurId) {
                    return $r->getFournisseurDeFournisseur($fournisseurId);
                },
            ))
            ->add('contient', ChoiceType::class, array(
                'choices' => array(
                    FournisseurContient::getLibelle(FournisseurContient::FOURNISSEUR) => FournisseurContient::FOURNISSEUR,
                    FournisseurContient::getLibelle(FournisseurContient::PRODUIT) => FournisseurContient::PRODUIT
                ),
                'choices_as_values' => true,
                'label' => 'contient',
                'translation_domain' => 'messages',
            ))
            ->add('interlocuteurs', CollectionType::class, array(
                    'entry_type' => 'Mondofute\Bundle\FournisseurBundle\Form\FournisseurInterlocuteurType',
                    'allow_add' => true,
                    'allow_delete' => true,
                    'label' => 'interlocuteurs',
                    'translation_domain' => 'messages',
                )
            )
            ->add('moyenComs',
                'Infinite\FormBundle\Form\Type\PolyCollectionType',
//                'Infinite\FormBundle\Form\Type\PolyCollectionType' ,
                array('types' => array(
//                    'Nucleus\MoyenComBundle\Form\AdresseType'
                    'nucleus_moyencombundle_adresse',
                    'nucleus_moyencombundle_email',
                    
                ),
                    'allow_add' => true,
//                    'allow_delete' => true,
                )
            );
            )
            ->add('remiseClefs', CollectionType::class, array(
                'entry_type' => RemiseClefType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'label' => 'remise.clef',
                'translation_domain' => 'messages',
            ))
            ->add('receptions', CollectionType::class, array(
                'entry_type' => ReceptionType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'label' => 'receptions',
                'translation_domain' => 'messages'
            ))
            ->add('reception', CollectionType::class, array(
                'entry_type' => ReceptionType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'label' => 'receptions',
                'translation_domain' => 'messages',
                'mapped' => false,
            ));
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
