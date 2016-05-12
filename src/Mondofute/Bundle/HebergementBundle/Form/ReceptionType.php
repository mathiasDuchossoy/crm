<?php

namespace Mondofute\Bundle\HebergementBundle\Form;

use Mondofute\Bundle\FournisseurBundle\Entity\Fournisseur;
use Mondofute\Bundle\TrancheHoraireBundle\Form\TrancheHoraireType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReceptionType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $optionsJour = array(
            '1' => 'lundi',
            '2' => 'mardi',
            '3' => 'mercredi',
            '4' => 'jeudi',
            '5' => 'vendredi',
            '6' => 'samedi',
            '0' => 'dimanche'
        );
        $builder
            ->add('jour', ChoiceType::class, array(
                'choices' => $optionsJour,
                'multiple' => true,
                'expanded' => true,
                'label' => 'jours',
                'translation_domain' => 'messages'
            ))
            ->add('tranche1', TrancheHoraireType::class,
                array('label' => 'tranche1', 'translation_domain' => 'messages'))
            ->add('tranche2', TrancheHoraireType::class,
                array('label' => 'tranche2', 'translation_domain' => 'messages'))
            ->add('fournisseur', EntityType::class, array(
                'class' => Fournisseur::class,
                'choice_label' => 'id',
                'label' => 'fournisseur',
                'translation_domain' => 'messages',
            ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mondofute\Bundle\HebergementBundle\Entity\Reception'
        ));
    }

    public function finishView(FormView $view, FormInterface $form, array $options)
    {
//        $i = 3;
//        foreach ($view->children['remiseClef']->vars['choices'] as $choice) {
//            $choice->attr['data-fournisseur'] = $choice->data->getFournisseur()->getId();
//        }
////        ajoute l'option 'nouveau' dans le select remise de clef
//        $option = new ChoiceView(array(), 'add', 'nouveau');
//        $view->children['remiseClef']->vars['choices'][] = $option;
    }
}
