<?php

namespace Mondofute\Bundle\GeographieBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\View\ChoiceView;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DepartementUnifieType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
//        dump();die;
//        $firstRegion = $builder->getData()->getDepartements()->First()->getRegion();
//        $siteRegion = (!empty($firstRegion)) ? $firstRegion->getSite() : null;
        $builder
            ->add('departements', CollectionType::class,
                array('entry_type' => DepartementType::class, 'entry_options' => array('locale' => $options["locale"])))
//            ->add('site')
//            ->add('regionUnifie')
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mondofute\Bundle\GeographieBundle\Entity\DepartementUnifie',
            'locale' => 'fr_FR'
        ));
    }

    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        $entities = 'departements';
        $entitySelect = 'region';
        foreach ($view->children[$entities]->children as $viewChild) {
            $siteId = $viewChild->vars['value']->getSite()->getId();
            $choices = $viewChild->children[$entitySelect]->vars['choices'];

            $newChoices = array();
            /** @var ChoiceView $choice */
            foreach ($choices as $key => $choice) {
                $choice->attr = array('data-unifie_id' => $choice->data->getRegionUnifie()->getId());
                if ($choice->data->getSite()->getId() == $siteId) {
                    $newChoices[$key] = $choice;
                }
            }
            $viewChild->children[$entitySelect]->vars['choices'] = $newChoices;
        }


        foreach ($view->children['departements'] as $departement) {
//            dump($departement);
            if ($departement->vars['value']->getSite()->getCrm() == 1) {
                $departementCrm = $departement;
            } else {
                foreach ($departement->children['images'] as $key => $image) {
                    if ($image->vars['value']->getActif() == true) {

//                        dump($departement->vars['value']->getSite()->getId());
                        $siteId = $departement->vars['value']->getSite()->getId();
//                    $departementCrm->children['images']->children[$key]
                        $departementCrm->children['images']->children[$key]->children['sites']->children[$siteId]->vars['attr'] = array('checked' => 'checked');
//                        dump($departementCrm->children['images']->children[$key]->children['sites']->children[$siteId]->vars['attr']);
                    }
                }
                foreach ($departement->children['photos'] as $key => $photo) {
                    if ($photo->vars['value']->getActif() == true) {

                        $siteId = $departement->vars['value']->getSite()->getId();
//                    $departementCrm->children['photos']->children[$key]
                        $departementCrm->children['photos']->children[$key]->children['sites']->children[$siteId]->vars['attr'] = array('checked' => 'checked');
                    }
                }
                foreach ($departement->children['videos'] as $key => $video) {
                    if ($video->vars['value']->getActif() == true) {

                        $siteId = $departement->vars['value']->getSite()->getId();
//                    $departementCrm->children['videos']->children[$key]
                        $departementCrm->children['videos']->children[$key]->children['sites']->children[$siteId]->vars['attr'] = array('checked' => 'checked');
                    }
                }
            }

        }
    }


}
