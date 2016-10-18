<?php

namespace Mondofute\Bundle\HebergementBundle\Form;

use Mondofute\Bundle\ServiceBundle\Entity\ListeService;
use Mondofute\Bundle\ServiceBundle\Form\ServiceHebergementType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
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
            ->add('listeService', EntityType::class, array(
                'class' => ListeService::class,
                'choice_label' => 'libelle',
                'placeholder' => '--- choix d\'une liste de service ---',
                'required' => false,
            ))
            ->add('services', CollectionType::class, array(
                'entry_type' => ServiceHebergementType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'allow_extra_fields' => true,
                'by_reference' => false
            ))
            ->add('stocks',TextType::class,array(
                'mapped' => false
            ))
            ->add('fournisseurs', CollectionType::class,
                array(
                    'entry_type' => FournisseurHebergementType::class,
                    'allow_add' => true,
                    'by_reference' => false,
                    'allow_delete' => true,
                ));
        $removeStocks = function($form,$stocks){
            if(!empty($stocks)){

            }
        };
        $builder->addEventListener(FormEvents::PRE_SET_DATA,function(FormEvent $event){
//            $data = $event->getData();
//            $form = $event->getForm();
//
////            $form
//            echo 'post_set_data';
//            dump($data);
//            dump($form);
//            die;
        });
        $builder->addEventListener(FormEvents::PRE_SUBMIT,function(FormEvent $event){
            $data = $event->getData();
            $form = $event->getForm();

//            $form
//            echo 'pre_submit';
//            if(array_key_exists('stocks',$data)){
//                dump($data);
//                dump($form);
//            }
//            die;
        });
    }
    public function supprimerStocks(FormEvent $event){
        echo 'ok';
        dump($event);
        die;
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

        $hebergementCrm = null;
        foreach ($view->children['hebergements'] as $hebergement) {
            if ($hebergement->vars['value']->getSite()->getCrm() == 1) {
                $hebergementCrm = $hebergement;
//                dump($hebergementCrm);
            } else {
                foreach ($hebergement->children['visuels'] as $key => $image) {
                    if ($image->vars['value']->getActif() == true) {

                        $siteId = $hebergement->vars['value']->getSite()->getId();
                        $hebergementCrm->children['visuels']->children[$key]->children['sites']->children[$siteId]->vars['attr'] = array('checked' => 'checked');
                    }
                }
            }
        }

        foreach ($view->children['listeService']->vars['choices'] as $choice) {
            $affiche = false;
            foreach ($view->vars['data']->getFournisseurs() as $fournisseur) {
                if ($choice->data->getFournisseur()->getId() == $fournisseur->getFournisseur()->getId()) {
                    $affiche = true;
                }
            }
            if ($affiche == false) {
                $choice->attr = array('style' => 'display:none;');
            }
        }
    }

}
