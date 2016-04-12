<?php

namespace Mondofute\Bundle\StationBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StationUnifieType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $stationUnifieId = $builder->getData()->getId();
        $builder
            ->add('stations', CollectionType::class, array(
                'entry_type' => StationType::class,
                'entry_options' => array(
                    'locale' => $options["locale"],
                    'stationUnifieId' => $stationUnifieId
                )
            ))
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mondofute\Bundle\StationBundle\Entity\StationUnifie',
            'locale' => 'fr_FR'
        ));
    }

    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        /** @var FormView $viewChild */
        $entities = 'stations';
        $entitiesSelect = array();
        $entitiesSelect[] = 'zoneTouristiques';
//        echo ucfirst('zoneTouristique');die;
        $entitiesSelect[] = 'secteurs';
        $entitiesSelect[] = 'profils';
        $entitiesSelect[] = 'departement';
        $entitiesSelect[] = 'domaine';
        foreach ($entitiesSelect as $entitySelect) {
            foreach ($view->children[$entities]->children as $viewChild) {
                $siteId = $viewChild->vars['value']->getSite()->getId();
                if ($entitySelect == 'secteur') $entitySelect = 'secteurs';
                if ($entitySelect == 'zoneTouristique') $entitySelect = 'zoneTouristiques';
                if ($entitySelect == 'profil') $entitySelect = 'profils';
                $choices = $viewChild->children[$entitySelect]->vars['choices'];
                $newChoices = array();
                foreach ($choices as $key => $choice) {
                    if ($entitySelect == 'secteurs') $entitySelect = 'secteur';
                    if ($entitySelect == 'zoneTouristiques') $entitySelect = 'zoneTouristique';
                    if ($entitySelect == 'profils') $entitySelect = 'profil';
                    $choice->attr = array('data-unifie_id' => $choice->data->{'get' . ucfirst($entitySelect . 'Unifie')}()->getId());
                    if ($choice->data->getSite()->getId() == $siteId) {
                        $newChoices[$key] = $choice;
                    }
                }

                if ($entitySelect == 'secteur') $entitySelect = 'secteurs';
                if ($entitySelect == 'zoneTouristique') $entitySelect = 'zoneTouristiques';
                if ($entitySelect == 'profil') $entitySelect = 'profils';
                $viewChild->children[$entitySelect]->vars['choices'] = $newChoices;

            }
        }


        $entities = 'stations';
        $entitySelect = 'stationMere';
        /** @var FormView $viewChild */
        foreach ($view->children[$entities]->children as $viewChild) {
            $siteId = $viewChild->vars['value']->getSite()->getId();
            $choices = $viewChild->children[$entitySelect]->vars['choices'];


            $newChoices = array();
            foreach ($choices as $key => $choice) {
//                dump($choice->data->getDomaine());
                $domaineId = !empty($choice->data->getDomaine()) ? $choice->data->getDomaine()->getId() : '';
                $choice->attr = array(
                    'data-unifie_id' => $choice->data->getStationUnifie()->getId(),
                    'data-domaine_id' => $domaineId
                );
                if ($choice->data->getSite()->getId() == $siteId) {
                    $newChoices[$key] = $choice;
                }
            }
            $viewChild->children[$entitySelect]->vars['choices'] = $newChoices;
        }

//        die;
    }
}
