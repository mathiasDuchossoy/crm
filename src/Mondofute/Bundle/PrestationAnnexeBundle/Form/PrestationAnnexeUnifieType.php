<?php

namespace Mondofute\Bundle\PrestationAnnexeBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
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
            ->add('prestationAnnexes', CollectionType::class, array(
                'entry_type' => PrestationAnnexeType::class,
                'entry_options' => array('locale' => $options["locale"])
            ));
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

    /**
     * @param FormView $view
     * @param FormInterface $form
     * @param array $options
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        $entities = 'prestationAnnexes';
        $entitySelect = 'sousFamillePrestationAnnexe';
        /** @var FormView $viewChild */
        foreach ($view->children[$entities]->children as $viewChild) {
            $choices = $viewChild->children[$entitySelect]->vars['choices'];

            $newChoices = array();
            foreach ($choices as $key => $choice) {
                $famillePrestationAnnexeId = !empty($choice->data->getFamillePrestationAnnexe()) ? $choice->data->getFamillePrestationAnnexe()->getId() : '';
                $choice->attr = array(
                    'data-famille_prestation_annexe_id' => $famillePrestationAnnexeId
                );
                $newChoices[$key] = $choice;
            }
            $viewChild->children[$entitySelect]->vars['choices'] = $newChoices;
        }
    }
}
