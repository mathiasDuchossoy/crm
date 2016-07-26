<?php

namespace Mondofute\Bundle\PeriodeBundle\Form;

use Mondofute\Bundle\PeriodeBundle\Entity\TypePeriode;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PeriodeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('debut', DateType::class, array(
                'required' => true,
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
                'attr' => array(
                    'class' => 'form-control input-inline datepicker date',
                    'data-provide' => 'datepicker',
                    'data-date-format' => 'dd/mm/yyyy',
                    'placeholder' => 'format_date',
                )
            ))
            ->add('fin', DateType::class, array(
                'required' => true,
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
                'attr' => array(
                    'class' => 'form-control input-inline datepicker date',
                    'data-provide' => 'datepicker',
                    'data-date-format' => 'dd/mm/yyyy',
                    'placeholder' => 'format_date',
                )
            ))
            ->add('nbJour', null, array('required' => false))
            ->add('type', EntityType::class, array(
                'class' => TypePeriode::class,
                'placeholder' => 'placeholder.choix_periode_type',
                'choice_translation_domain' => 'messages',
                'translation_domain' => 'messages',
                'required' => true,
//                'choice_label' => 'id',
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array('data_class' => 'Mondofute\Bundle\PeriodeBundle\Entity\Periode'));
    }

    public function getName()
    {
        return 'mondofute_periode_bundle_periode';
    }
}
