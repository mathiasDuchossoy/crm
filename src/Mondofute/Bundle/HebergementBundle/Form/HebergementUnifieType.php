<?php

namespace Mondofute\Bundle\HebergementBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HebergementUnifieType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('hebergements', CollectionType::class,
                array('entry_type' => HebergementType::class, 'entry_options' => array('locale' => $options["locale"])))
            ->add('fournisseurs', CollectionType::class,
                array(
                    'entry_type' => FournisseurHebergementType::class
                ,
                    'allow_add' => true
                ))
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mondofute\Bundle\HebergementBundle\Entity\HebergementUnifie',
            'locale' => 'fr_FR',
        ));
    }

    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        $entities = 'hebergements';
//        $entitySelect = 'station';
//        foreach ($view->children[$entities]->children as $viewChild) {
//            $siteId = $viewChild->vars['value']->getSite()->getId();
//            $choices = $viewChild->children[$entitySelect]->vars['choices'];
//
//            $newChoices = array();
//            /** @var ChoiceView $choice */
//            foreach ($choices as $key => $choice) {
//                $choice->attr = array('data-unifie_id' => $choice->data->getStationUnifie()->getId());
//                if ($choice->data->getSite()->getId() == $siteId) {
//                    $newChoices[$key] = $choice;
//                }
//            }
//            $viewChild->children[$entitySelect]->vars['choices'] = $newChoices;
//        }
        $entitiesSelect = array();
        $entitiesSelect[] = 'station';
//        echo ucfirst('zoneTouristique');die;
        $entitiesSelect[] = 'typeHebergement';
//        $entitiesSelect[] = 'departement';
//        $entitiesSelect[] = 'domaine';
        foreach ($entitiesSelect as $entitySelect) {
            foreach ($view->children[$entities]->children as $viewChild) {
                $siteId = $viewChild->vars['value']->getSite()->getId();
                $choices = $viewChild->children[$entitySelect]->vars['choices'];

                $newChoices = array();
                foreach ($choices as $key => $choice) {
                    $choice->attr = array('data-unifie_id' => $choice->data->{'get' . ucfirst($entitySelect . 'Unifie')}()->getId());
                    if ($choice->data->getSite()->getId() == $siteId) {
                        $newChoices[$key] = $choice;
                    }
                }
                $viewChild->children[$entitySelect]->vars['choices'] = $newChoices;
            }
        }
    }
}
