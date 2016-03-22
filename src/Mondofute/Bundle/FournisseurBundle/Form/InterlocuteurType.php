<?php

namespace Mondofute\Bundle\FournisseurBundle\Form;

use Mondofute\Bundle\FournisseurBundle\Entity\InterlocuteurFonction;
use Mondofute\Bundle\FournisseurBundle\Entity\ServiceInterlocuteur;
use Mondofute\Bundle\FournisseurBundle\Repository\InterlocuteurFonctionRepository;
use Mondofute\Bundle\FournisseurBundle\Repository\ServiceInterlocuteurRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
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
        $locale = $options['locale'];
        $builder
            ->add('prenom')
            ->add('nom')
            ->add('fonction', EntityType::class, array(
                    'class' => InterlocuteurFonction::class,
                    'placeholder' => '--- choisir une fonction ---',
//                    'required' => false,
                    'choice_label' => 'traductions[0].libelle',
                    'query_builder' => function (InterlocuteurFonctionRepository $r) use ($locale) {
                        return $r->getTraductionsByLocale($locale);
                    },
                )
            )
            ->add('service', EntityType::class, array(
                    'class' => ServiceInterlocuteur::class,
                    'placeholder' => '--- choisir un service ---',
//                    'required' => false,
                    'choice_label' => 'traductions[0].libelle',
                    'query_builder' => function (ServiceInterlocuteurRepository $r) use ($locale) {
                        return $r->getTraductionsByLocale($locale);
                    },
                )
            )
            ->add('moyenCommunications'
                , CollectionType::class
                , array(
                    'entry_type' => 'Mondofute\Bundle\FournisseurBundle\Form\InterlocuteurMoyenCommunicationType',
                    'allow_add' => true
                )
            )

//            ->add('fixe', 'commun\moyencommunicationBundle\Form\FixeType' , array('label' => true , 'label_attr' => array('style' => 'display:none')))
//            ->add('telephone1', 'commun\moyencommunicationBundle\Form\FixeType' , array('label' => 'téléphone 1'))
//            ->add('telephone2', 'commun\moyencommunicationBundle\Form\FixeType' , array('label' => 'téléphone 2'))
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mondofute\Bundle\FournisseurBundle\Entity\Interlocuteur',
            'locale' => 'fr_FR'
        ));
    }

    public function finishView(FormView $view, FormInterface $form, array $options)
    {
//        $moyenCommunications = $view->children['moyenCommunications'];
//        dump($moyenCommunications);
//        $moyenCommunications->vars

//        die;
//        $entities = 'moyenCommunications';
//        $entitySelect = 'interlocuteur';
//        foreach ($view->children[$entities]->children as $viewChild) {
//            $siteId = $viewChild->vars['value']->getSite()->getId();
//            $choices = $viewChild->children[$entitySelect]->vars['choices'];
//
//            $newChoices = array();
//            foreach ($choices as $key => $choice) {
//                if ($choice->data->getSite()->getId() == $siteId) {
//                    $newChoices[$key] = $choice;
//                }
//            }
//            $viewChild->children[$entitySelect]->vars['choices'] = $newChoices;
//        }
    }

}
