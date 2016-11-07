<?php

namespace Mondofute\Bundle\HebergementBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HebergementTraductionType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom')
            ->add('avisMondofute')
            ->add('restauration', null, array('required' => false))
            ->add('bienEtre', null, array('required' => false))
            ->add('pourLesEnfants', null, array('required' => false))
            ->add('activites', null, array('required' => false))
            ->add('langue', HiddenType::class, array('mapped' => false))
            ->add('accroche', null, array('required' => false))
            ->add('generalite', null, array('required' => false))
            ->add('avisHebergement', null, array('required' => false))
            ->add('avisLogement', null, array('required' => false))
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mondofute\Bundle\HebergementBundle\Entity\HebergementTraduction'
        ));
    }
}
