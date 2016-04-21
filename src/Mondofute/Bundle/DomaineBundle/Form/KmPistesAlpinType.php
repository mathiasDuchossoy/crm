<?php

namespace Mondofute\Bundle\DomaineBundle\Form;

use Mondofute\Bundle\UniteBundle\Form\DistanceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class KmPistesAlpinType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('longueur', DistanceType::class, array('label' => array('style' => 'display:none')));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mondofute\Bundle\DomaineBundle\Entity\KmPistesAlpin'
        ));
    }

    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        /** @var FormView $viewChild */
        $entities = array('longueur');
        foreach ($entities as $entity) {
            foreach ($view->children[$entity]->children as $child) {
                $child->vars['attr'] = array('data-unique_block_prefix' => $child->vars['unique_block_prefix']);
            }
        }
    }
}
