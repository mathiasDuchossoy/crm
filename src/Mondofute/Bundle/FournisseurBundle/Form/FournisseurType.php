<?php

namespace Mondofute\Bundle\FournisseurBundle\Form;

use Mondofute\Bundle\FournisseurBundle\Entity\Fournisseur;
use Mondofute\Bundle\FournisseurBundle\Entity\FournisseurContient;
use Mondofute\Bundle\FournisseurBundle\Repository\FournisseurRepository;
use ReflectionClass;
use Mondofute\Bundle\RemiseClefBundle\Form\RemiseClefType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
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
                    'by_reference' => false,
                )
            )
            ->add('moyenComs',
                'Infinite\FormBundle\Form\Type\PolyCollectionType',
//                'Infinite\FormBundle\Form\Type\PolyCollectionType' ,
                array('types' => array(
//                    'Nucleus\MoyenComBundle\Form\AdresseType'
                    'nucleus_moyencombundle_adresse',
//                    'nucleus_moyencombundle_email',
                    
                ),
                    'allow_add' => true,
//                    'allow_delete' => true,
                    'by_reference' => false,
                )
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


    public function finishView(FormView $view, FormInterface $form, array $options)
    {
//        dump($view->children['interlocuteurs']->children[0]->children['interlocuteur']->children['moyenComs']->children);
        // ordre d'affichage: Adresse , Email, Téléphone 1, Téléphone 2, Mobile
        $interlocuteurs = $view->children['interlocuteurs']->children;
        foreach ($interlocuteurs as $interlocuteur) {
            $cViewComm = [
                'Adresse' => [],
                'Email' => [],
                'TelFixe' => [],
                'TelMobile' => []
            ];
//            $cViewComm = [];
            foreach ($interlocuteur->children['interlocuteur']->children['moyenComs']->children as $viewMoyenComs) {
                $typeComm = (new ReflectionClass($viewMoyenComs->vars['value']))->getShortName();
                dump($typeComm);
                $viewMoyenComs->vars['type'] = $typeComm;
                $viewMoyenComs->vars['label'] = $typeComm;
                if (empty($cViewComm[$typeComm])) {
                    $cViewComm[$typeComm] = [];
                }
                array_push($cViewComm[$typeComm], $viewMoyenComs);
            }
            foreach ($cViewComm as $viewCom) {
                foreach ($viewCom as $key => $com) {
                    if ($key > 0) {
                        $com->vars['label'] = $com->vars['label'] . ' ' . ($key + 1);
                    }
                }
//                dump($viewCom);
            }
            $interlocuteur->children['interlocuteur']->children['moyenComs']->children = [];

            $i = 0;
            foreach ($cViewComm as $viewCom) {
                foreach ($viewCom as $com) {
                    $com->vars['name'] = $i;
                    array_push($interlocuteur->children['interlocuteur']->children['moyenComs']->children, $com);
                    $i++;
                }
            }
//            dump($interlocuteur->children['interlocuteur']->children['moyenComs']->children);

        }
//        die;
    }


}
