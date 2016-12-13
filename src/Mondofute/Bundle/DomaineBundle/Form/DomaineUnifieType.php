<?php

namespace Mondofute\Bundle\DomaineBundle\Form;

use Mondofute\Bundle\DomaineBundle\Entity\Domaine;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DomaineUnifieType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var Domaine $firstDomaineParent */
        $domaineUnifieId = $builder->getData()->getId();
        $firstDomaineParent = $builder->getData()->getDomaines()->First()->getDomaineParent();
        $siteDomaineParent = (!empty($firstDomaineParent)) ? $firstDomaineParent->getSite() : null;
        $builder
            ->add('domaines', CollectionType::class, array(
                    'entry_type' => DomaineType::class,
                    'entry_options' => array(
                        'locale' => $options['locale'],
                        'siteDomaineParent' => $siteDomaineParent,
                        'domaineUnifieId' => $domaineUnifieId
                    )
                )
            );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mondofute\Bundle\DomaineBundle\Entity\DomaineUnifie',
            'locale' => 'fr_FR'
        ));
    }

    /**
     * @param FormView $view
     * @param FormInterface $form
     * @param array $options
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        $entities = 'domaines';
        $entitySelect = 'domaineParent';
        /** @var FormView $viewChild */
        foreach ($view->children[$entities]->children as $viewChild) {
            $siteId = $viewChild->vars['value']->getSite()->getId();
            $choices = $viewChild->children[$entitySelect]->vars['choices'];

            $newChoices = array();
            foreach ($choices as $key => $choice) {
                $choice->attr = array('data-unifie_id' => $choice->data->getDomaineUnifie()->getId());
                if ($choice->data->getSite()->getId() == $siteId) {
                    $newChoices[$key] = $choice;
                }
            }
            $viewChild->children[$entitySelect]->vars['choices'] = $newChoices;
        }

        foreach ($view->children['domaines'] as $domaine) {
            if ($domaine->vars['value']->getSite()->getCrm() == 1) {
                $domaineCrm = $domaine;
            } else {
                foreach ($domaine->children['images'] as $key => $image) {
                    if ($image->vars['value']->getActif() == true) {
                        $siteId = $domaine->vars['value']->getSite()->getId();
                        $domaineCrm->children['images']->children[$key]->children['sites']->children[$siteId]->vars['attr'] = array('checked' => 'checked');
                    }
                }
                foreach ($domaine->children['photos'] as $key => $photo) {
                    if ($photo->vars['value']->getActif() == true) {

                        $siteId = $domaine->vars['value']->getSite()->getId();
                        $domaineCrm->children['photos']->children[$key]->children['sites']->children[$siteId]->vars['attr'] = array('checked' => 'checked');
                    }
                }
                foreach ($domaine->children['videos'] as $key => $video) {
                    if ($video->vars['value']->getActif() == true) {

                        $siteId = $domaine->vars['value']->getSite()->getId();
                        $domaineCrm->children['videos']->children[$key]->children['sites']->children[$siteId]->vars['attr'] = array('checked' => 'checked');
                    }
                }
            }

        }

    }


}
