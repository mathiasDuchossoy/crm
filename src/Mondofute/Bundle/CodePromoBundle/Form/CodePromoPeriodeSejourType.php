<?php

namespace Mondofute\Bundle\CodePromoBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CodePromoPeriodeSejourType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('dateDebut', DateType::class, array(
                'required' => true,
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
                'attr' => array(
//                    'class' => 'form-control input-inline datepicker date',
//                    'class' => 'form-control input-inline date',
                    'data-date-format' => 'dd/mm/yyyy',
                    'placeholder' => 'format_date',
                )
            ))
            ->add('dateFin', DateType::class, array(
                'required' => true,
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
                'attr' => array(
//                    'class' => 'form-control input-inline datepicker date',
//                    'class' => 'form-control input-inline date',
                    'data-date-format' => 'dd/mm/yyyy',
                    'placeholder' => 'format_date',
                )
            ))
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mondofute\Bundle\CodePromoBundle\Entity\CodePromoPeriodeSejour'
        ));
    }
}
