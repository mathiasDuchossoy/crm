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
                ));
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
        $entitiesSelect = array();
        $entitiesSelect[] = 'station';
        $entitiesSelect[] = 'typeHebergement';
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

        foreach ($view->children['hebergements'] as $hebergement) {
            if ($hebergement->vars['value']->getSite()->getCrm() == 1) {
                $hebergementCrm = $hebergement;
            } else {
                foreach ($hebergement->children['visuels'] as $key => $image) {
                    if ($image->vars['value']->getActif() == true) {

                        $siteId = $hebergement->vars['value']->getSite()->getId();
                        $hebergementCrm->children['visuels']->children[$key]->children['sites']->children[$siteId]->vars['attr'] = array('checked' => 'checked');
                    }
                }
            }

        }
        
    }


}
