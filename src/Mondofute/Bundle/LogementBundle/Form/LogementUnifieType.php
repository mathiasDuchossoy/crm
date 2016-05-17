<?php

namespace Mondofute\Bundle\LogementBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LogementUnifieType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('logements', CollectionType::class,
            array('entry_type' => LogementType::class, 'entry_options' => array('locale' => $options["locale"])));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mondofute\Bundle\LogementBundle\Entity\LogementUnifie',
            'locale' => 'fr_FR'
        ));
    }

//    public function finishView(FormView $view, FormInterface $form, array $options)
//    {
//        $entities = 'departements';
//        $entitySelect = 'region';
//        foreach ($view->children[$entities]->children as $viewChild) {
//            $siteId = $viewChild->vars['value']->getSite()->getId();
//            $choices = $viewChild->children[$entitySelect]->vars['choices'];
//
//            $newChoices = array();
//            /** @var ChoiceView $choice */
//            foreach ($choices as $key => $choice) {
//                $choice->attr = array('data-unifie_id' => $choice->data->getRegionUnifie()->getId());
//                if ($choice->data->getSite()->getId() == $siteId) {
//                    $newChoices[$key] = $choice;
//                }
//            }
//            $viewChild->children[$entitySelect]->vars['choices'] = $newChoices;
//        }
//    }
}
