<?php

namespace Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PeriodeValiditeType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('dateDebut', DateTimeType::class , array(
                'required' => true,
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy - HH:mm',//yyyy-MM-dd'T'HH:mm:ssZZZZZ
                'attr' => array(
                    'class' => 'form-control input-inline datetimepicker datetime',
                    'data-date-format' => 'dd/MM/yyyy HH:mm',
                    'placeholder' => 'format_date',
                )
            ))
            ->add('dateFin', DateTimeType::class , array(
                'required' => true,
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy - HH:mm',//yyyy-MM-dd'T'HH:mm:ssZZZZZ
                'attr' => array(
                    'class' => 'form-control input-inline datetimepicker datetime',
                    'data-date-format' => 'dd/MM/yyyy - HH:mm',
                    'placeholder' => 'format_date',
                )
            ))
//            ->add('dateDebut', DateType::class, array(
//                'required' => true,
//                'widget' => 'single_text',
//                'format' => 'dd/mm/yyyy hh:ii:ss',
//                'attr' => array(
//                    'class' => 'form-control input-inline datepicker date',
////                    'data-provide' => 'datepicker-futur-tranche-cinq-ans',
//                    'data-date-format' => 'dd/mm/yyyy hh:ii:ss',
//                    'placeholder' => 'format_date',
//                )
//            ))
//            ->add('dateFin', DateType::class, array(
//                'required' => true,
//                'widget' => 'single_text',
//                'format' => 'dd/mm/yyyy hh:ii:ss',
//                'attr' => array(
//                    'class' => 'form-control input-inline datepicker date',
////                    'data-provide' => 'datepicker-futur-tranche-cinq-ans',
//                    'data-date-format' => 'dd/mm/yyyy hh:ii:ss',
//                    'placeholder' => 'format_date',
//                )
//            ))
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity\PeriodeValidite'
        ));
    }
}
