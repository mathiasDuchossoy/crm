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
        $builder
            ->add('stations', CollectionType::class, array('entry_type' => StationType::class, 'entry_options' => array('locale' => $options["locale"])))
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
        $entitiesSelect[] = 'zoneTouristique';
//        echo ucfirst('zoneTouristique');die;
        $entitiesSelect[] = 'secteur';
        $entitiesSelect[] = 'departement';
        $entitiesSelect[] = 'domaine';
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
