<?php

namespace Mondofute\Bundle\FournisseurBundle\Form;

use Mondofute\Bundle\FournisseurBundle\Entity\InterlocuteurFonction;
use Mondofute\Bundle\FournisseurBundle\Entity\InterlocuteurUser;
use Mondofute\Bundle\FournisseurBundle\Entity\ServiceInterlocuteur;
use Mondofute\Bundle\FournisseurBundle\Repository\InterlocuteurFonctionRepository;
use Mondofute\Bundle\FournisseurBundle\Repository\ServiceInterlocuteurRepository;
use ReflectionClass;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InterlocuteurType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $fournisseurId = $options['fournisseurId'];
        $test = $builder->getData();

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $product = $event->getData();
            $form = $event->getForm();

            dump($product);
            // vérifie si l'objet Product est "nouveau"
            // Si aucune donnée n'est passée au formulaire, la donnée est "null".
            // Ce doit être considéré comme un nouveau "Product"
//            if (!$product || null === $product->getId()) {
//                $form->add('name', 'text');
//            }
        });
//        dump($test);
        $locale = $options['locale'];
        $builder
            ->add('prenom')
            ->add('nom')
            ->add('fonction', EntityType::class, array(
                    'class' => InterlocuteurFonction::class,
                    'placeholder' => 'placeholder.choisir.fonction',
//                    'required' => false,
                    'choice_label' => 'traductions[0].libelle',
                    'label' => 'fonction',
                    'translation_domain' => 'messages',
                    'query_builder' => function (InterlocuteurFonctionRepository $r) use ($locale) {
                        return $r->getTraductionsByLocale($locale);
                    },
                )
            )
            ->add('service', EntityType::class, array(
                    'class' => ServiceInterlocuteur::class,
                    'placeholder' => 'placeholder.choisir.service',
//                    'required' => false,
                    'choice_label' => 'traductions[0].libelle',
                    'label' => 'service',
                    'translation_domain' => 'messages',
                    'query_builder' => function (ServiceInterlocuteurRepository $r) use ($locale) {
                        return $r->getTraductionsByLocale($locale);
                    },
                )
            )
            ->add('moyenComs',
//                'infinite_form_polycollection',
                'Infinite\FormBundle\Form\Type\PolyCollectionType',
                array('types' => array(
//                    'Nucleus\MoyenComBundle\Form\AdresseType'
                    'nucleus_moyencombundle_adresse',
                    'nucleus_moyencombundle_email',
                    'nucleus_moyencombundle_telfixe',
                    'nucleus_moyencombundle_telmobile',
                ),
                    'prototype_name' => '__mycom_name__',
                    'allow_add' => true,
                    'by_reference' => false,
                    'required' => true
                )
            )
            ->add('user', InterlocuteurUserType::class, array(
                'data_class' => InterlocuteurUser::class
            ))
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mondofute\Bundle\FournisseurBundle\Entity\Interlocuteur',
            'locale' => 'fr_FR',
            'fournisseurId' => null,
        ));
    }


    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        parent::finishView($view, $form, $options);
        $cViewComm = [];
        foreach ($view->children['moyenComs']->children as $viewMoyenComs) {
            $typeComm = (new ReflectionClass($viewMoyenComs->vars['value']))->getShortName();
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
        }

//        $typeComm = (new ReflectionClass($moyenCom))->getShortName();
//
//        if ($typeComm == 'Email' && empty($login)) {
//            $login = $moyenCom->getAdresse();
//            $utilisateurUser
//                ->setUsername($login)
//                ->setEmail($login);
//        }
    }


//    public function finishView(FormView $view, FormInterface $form, array $options)
//    {
//        parent::finishView($view, $form, $options); // TODO: Change the autogenerated stub
//        $moyenComs = $view->children['moyenComs'];
//        foreach ($moyenComs->children as $moyenCom)
//        {
//            dump($moyenCom);
//        }
//        dump($form);die;
//
//    }

}
