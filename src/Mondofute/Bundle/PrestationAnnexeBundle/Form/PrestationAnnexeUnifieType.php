<?php

namespace Mondofute\Bundle\PrestationAnnexeBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PrestationAnnexeUnifieType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('prestationAnnexes', CollectionType::class, array('entry_type' => PrestationAnnexeType::class, 'entry_options' => array('locale' => $options["locale"])))
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mondofute\Bundle\PrestationAnnexeBundle\Entity\PrestationAnnexeUnifie',
            'locale' => 'fr_FR'
        ));
    }

    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        // todo: faire finish view  avec dependance famille sousfamille
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
    }

}
