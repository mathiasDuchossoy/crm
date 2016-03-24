<?php

namespace Mondofute\Bundle\StationBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StationCarteIdentiteType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('codePostal', IntegerType::class)
            ->add('jourOuverture', IntegerType::class, array('attr' => array('max' => 31)))
            ->add('moisOuverture', IntegerType::class, array('attr' => array('max' => 12)))
            ->add('jourFermeture', IntegerType::class, array('attr' => array('max' => 31)))
            ->add('moisFermeture', IntegerType::class, array('attr' => array('max' => 12)))
            ->add('altitudeVillage', 'Mondofute\Bundle\UniteBundle\Form\DistanceType')
            ->add('site', HiddenType::class, array('mapped' => false))//            ->add('stationCarteIdentiteUnifie')
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mondofute\Bundle\StationBundle\Entity\StationCarteIdentite'
        ));
    }


    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        /** @var FormView $viewChild */
        foreach ($view->children['altitudeVillage']->children as $child) {
            $child->vars['attr'] = array('data-unique_block_prefix' => $child->vars['unique_block_prefix']);
        }
    }

}
