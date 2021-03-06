<?php

namespace Mondofute\Bundle\HebergementBundle\Form;

use Mondofute\Bundle\FournisseurBundle\Entity\Fournisseur;
use Mondofute\Bundle\HebergementBundle\Entity\Reception;
use Mondofute\Bundle\HebergementBundle\Repository\ReceptionRepository;
use Mondofute\Bundle\RemiseClefBundle\Entity\RemiseClef;
use Mondofute\Bundle\SaisonBundle\Form\SaisonCodePasserelleType;
use Nucleus\MoyenComBundle\Entity\Adresse;
use Nucleus\MoyenComBundle\Entity\TelFixe;
use Nucleus\MoyenComBundle\Entity\TelMobile;
use Nucleus\MoyenComBundle\Form\AdresseType;
use Nucleus\MoyenComBundle\Form\TelFixeType;
use Nucleus\MoyenComBundle\Form\TelMobileType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\View\ChoiceView;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
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
            ->add('fournisseur', EntityType::class, array(
                'class' => Fournisseur::class,
                'choice_label' => 'enseigne',
                'attr' => array('style' => 'display:none'),
                'label_attr' => array('style' => 'display: none')
            ))
            ->add('telFixe', TelFixeType::class, array('data_class' => TelFixe::class))
            ->add('telMobile', TelMobileType::class, array('data_class' => TelMobile::class))
            ->add('adresse', AdresseType::class, array('data_class' => Adresse::class))
            ->add('traductions', CollectionType::class, array(
                'entry_type' => FournisseurHebergementTraductionType::class,
                'allow_add' => true,
                'prototype_name' => '__tradname__',
                'label_attr' => array('style' => 'display:none')
            ))
            ->add('remiseClef', EntityType::class, array(
                'class' => RemiseClef::class,
                'placeholder' => 'Veuillez choisir un profil de remise de clef',
                'choice_label' => 'libelle',
            ))
            ->add('receptions', EntityType::class, array(
                'class' => Reception::class,
//                'choice_label' => 'id',
                'choice_label' => function ($reception) {
                    $optionsJour = array(
                        '1' => 'lundi',
                        '2' => 'mardi',
                        '3' => 'mercredi',
                        '4' => 'jeudi',
                        '5' => 'vendredi',
                        '6' => 'samedi',
                        '0' => 'dimanche'
                    );
                    $libelle = $optionsJour[intval($reception->getJour(),
                            10)] . ' de ' . $reception->getTranche1()->getDebut()->format('H:i') . ' à ' . $reception->getTranche1()->getFin()->format('H:i');
                    if (!empty($reception->getTranche2())) {
                        if ($reception->getTranche2()->getDebut() != $reception->getTranche2()->getFin()) {
                            $libelle .= ' et de ' . $reception->getTranche2()->getDebut()->format('H:i') . ' à ' . $reception->getTranche2()->getFin()->format('H:i');
                        }
                    }
                    return $libelle;
                },
                'multiple' => true,
                'expanded' => true,
                'query_builder' => function (ReceptionRepository $rr) {
                    $qb = $rr->createQueryBuilder('r')
                        ->leftJoin('r.tranche1', 't1')
                        ->leftJoin('r.tranche2', 't2')
                        ->orderBy('r.jour', 'ASC')
                        ->addOrderBy('t1.debut', 'ASC')
                        ->addOrderBy('t1.fin', 'ASC')
                        ->addOrderBy('t2.debut', 'ASC')
                        ->addOrderBy('t2.fin', 'ASC');
                    return $qb;
                }
            ))
            ->add('button', ButtonType::class,
                array(
                    'label' => 'ajouter',
                    'attr' => array('class' => 'btn btn-default addReception', 'title' => 'ajouter.reception')
                ))
            ->add('saisonCodePasserelles', CollectionType::class,
                [
                    'prototype_name' => '__name_saison_code_passerelle_label__',
                    'entry_type' => SaisonCodePasserelleType::class,
                    'allow_add' => true,
                    'by_reference' => false,
                    'allow_delete' => true
                ]);

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

    public function finishView(FormView $view, FormInterface $form, array $options)
    {

//        parent::finishView($view, $form, $options); // TODO: Change the autogenerated stub
//        Ajoute l'attr data-fournisseur aux options du select
        foreach ($view->children['remiseClef']->vars['choices'] as $choice) {
            if (!empty($choice->data->getFournisseur())) {
                $choice->attr['data-fournisseur'] = $choice->data->getFournisseur()->getId();
            }
        }
//        Ajoute l'attr data-fournisseur aux options du select
        foreach ($view->children['receptions']->vars['choices'] as $key => $choice) {
            if (!empty($choice->data->getFournisseur())) {
                $idFournisseur = $choice->data->getFournisseur()->getId();
                $value[$choice->value] = $idFournisseur;
                $choice->attr['data-fournisseur'] = $idFournisseur;
            }
        }
        foreach ($view->children['receptions']->children as $reception) {
            $reception->vars['attr']['data-fournisseur'] = $value[$reception->vars['value']];
        }
//        ajoute l'option 'nouveau' dans le select remise de clef
        $option = new ChoiceView(array(), 'add', 'nouveau');
        $view->children['remiseClef']->vars['choices'][] = $option;
    }
}
