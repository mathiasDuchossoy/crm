<?php

namespace Mondofute\Bundle\FournisseurBundle\Form;

use Mondofute\Bundle\FournisseurBundle\Entity\ConditionAnnulation;
use Mondofute\Bundle\FournisseurBundle\Entity\FournisseurContient;
use Mondofute\Bundle\FournisseurBundle\Entity\Priorite;
use Mondofute\Bundle\FournisseurBundle\Entity\RelocationAnnulation;
use Mondofute\Bundle\FournisseurBundle\Repository\FournisseurRepository;
use Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Form\FournisseurPrestationAnnexeType;
use Mondofute\Bundle\PrestationAnnexeBundle\Entity\FamillePrestationAnnexe;
use Mondofute\Bundle\PrestationAnnexeBundle\Repository\FamillePrestationAnnexeRepository;
use Mondofute\Bundle\RemiseClefBundle\Form\RemiseClefType;
use Mondofute\Bundle\ServiceBundle\Form\ListeServiceType;
use Mondofute\Bundle\StationBundle\Entity\Station;
use Mondofute\Bundle\StationBundle\Repository\StationRepository;
use ReflectionClass;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
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
        $locale = $options["locale"];

        $builder
            ->add('logo', 'sonata_media_type', array(
                'provider' => 'sonata.media.provider.image',
                'context' => 'fournisseur_logo_crm',
                'required' => false,
                'label' => 'logo',
            ));

        $builder
            ->add('raisonSociale')
            ->add('types', EntityType::class, array(
                'class' => FamillePrestationAnnexe::class,
                'required' => true,
                "choice_label" => "traductions[0].libelle",
                "placeholder" => " --- choisir un type ---",
                'query_builder' => function (FamillePrestationAnnexeRepository $r) use ($locale) {
                    return $r->getTraductionsByLocale($locale);
                },
                'multiple' => true,
                'expanded' => true,
                'attr' => array(
                    'onclick' => "javascript:updatePrestationAnnexe('$fournisseurId',this);displayInformationRM()",
                )
            ))
            ->add('enseigne', null, array(
                'label' => 'enseigne',
                'translation_domain' => 'messages',
                'required' => true,
            ))
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
                    'entry_options' => array(
                        'fournisseurId' => $fournisseurId,
                    )
                )
            )
            ->add('moyenComs',
                'Infinite\FormBundle\Form\Type\PolyCollectionType',
//                'Infinite\FormBundle\Form\Type\PolyCollectionType' ,
                array(
                    'types' => array(
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
            ))
            ->add('listeServices', CollectionType::class, array(
                'entry_type' => ListeServiceType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'label' => 'liste_service',
                'translation_domain' => 'messages',
                'prototype_name' => '__liste_service_name__',
            ))
            ->add('commentaires', CollectionType::class, array(
                'entry_type' => FournisseurCommentaireType::class,
                'allow_extra_fields' => true,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'label' => false,
                'translation_domain' => 'messages',
                'prototype_name' => '__fournisseur_commentaire_name__',
                'required' => false,
                'empty_data' => null,
            ))
            ->add('prestationAnnexes', CollectionType::class, array(
                'entry_type' => FournisseurPrestationAnnexeType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'label' => 'prestation.annexe',
                'translation_domain' => 'messages',
                'prototype_name' => '__prestation_annexe_name__',
                'options' => array(
                    'famillePrestationAnnexeId' => $options['famillePrestationAnnexeId']
                )
            ))
            ->add('phototheque')
//            Clauses contractuelles
            ->add('specificiteCommission')
            ->add('retrocommissionMFFinSaison')
            ->add('conditionAnnulation', ChoiceType::class, array(
                    'choices' => array(
                        ConditionAnnulation::getLibelle(ConditionAnnulation::standard) => ConditionAnnulation::standard,
                        ConditionAnnulation::getLibelle(ConditionAnnulation::personnalisee) => ConditionAnnulation::personnalisee
                    ),
                    'choices_as_values' => true,
                    "placeholder" => " --- choisir une condition d'annulation ---",
                    'required' => false
                )
            )
            ->add('conditionAnnulationDescription', ConditionAnnulationDescriptionType::class, ['required' => false])
            ->add('relocationAnnulation', ChoiceType::class, array(
                    'choices' => array(
                        RelocationAnnulation::getLibelle(RelocationAnnulation::nsp) => RelocationAnnulation::nsp,
                        RelocationAnnulation::getLibelle(RelocationAnnulation::oui) => RelocationAnnulation::oui,
                        RelocationAnnulation::getLibelle(RelocationAnnulation::non) => RelocationAnnulation::non,
                        RelocationAnnulation::getLibelle(RelocationAnnulation::casParCas) => RelocationAnnulation::casParCas
                    ),
                    'choices_as_values' => true,
                )
            )
            ->add('delaiPaiementFacture', IntegerType::class, array(
                'attr' => array(
                    'max' => 0
                )
            ))
//          fin Clauses contractuelles
//          Informations RM
            ->add('lieuRetraitForfaitSki')
            ->add('commissionForfaitFamille')
            ->add('commissionForfaitPeriode')
            ->add('commissionSupportMainLibre')
//          Fin Informations RM
            ->add('blocageVente', IntegerType::class, array(
                'label' => 'Blocage vente J-'
            ))
            ->add('priorite', ChoiceType::class, array(
                    'choices' => array(
                        Priorite::getLibelle(Priorite::NC) => Priorite::NC,
                        Priorite::getLibelle(Priorite::priorite1) => Priorite::priorite1,
                        Priorite::getLibelle(Priorite::priorite2) => Priorite::priorite2,
                        Priorite::getLibelle(Priorite::priorite3) => Priorite::priorite3
                    ),
                    'choices_as_values' => true,
                )
            )
            ->add('station', EntityType::class, [
                'class' => Station::class,
                'query_builder' => function (StationRepository $r) use ($locale) {
                    return $r->getTraductionsByLocale($locale, null, 1);
                },
                'empty_value' => ' --- Choisir une station --- ',
                'choice_label' => 'traductions[0].libelle'
            ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mondofute\Bundle\FournisseurBundle\Entity\Fournisseur',
            'locale' => 'fr_FR',
            'famillePrestationAnnexeId' => null,
        ));
    }


    public function finishView(FormView $view, FormInterface $form, array $options)
    {
//        $arrayType = new ArrayCollection();
        $view->children['logo']->children['binaryContent']->vars['attr'] = array('accept' => "image/x-png, image/gif, image/jpeg");

//        if (!empty($view->vars['value']->getTypes())) {
//            foreach ($view->vars['value']->getTypes() as $type) {
//                $arrayType->add($type->getTypeFournisseur());
//            }
//
//            foreach ($view->children['typeFournisseurs']->children as $checkBoxTypeFournisseur) {
//                if ($arrayType->contains(intval($checkBoxTypeFournisseur->vars['value']))) {
//                    $checkBoxTypeFournisseur->vars['attr']['checked'] = "checked";
//                }
//            }
//        }

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
//                dump($typeComm);
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

        }
    }


}
