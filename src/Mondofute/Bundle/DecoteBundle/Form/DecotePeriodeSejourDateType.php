<?php

namespace Mondofute\Bundle\DecoteBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DecotePeriodeSejourDateType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('dateDebut', DateTimeType::class,
                array(
                    'required' => true,
                    'widget' => 'single_text',
                    'format' => 'dd/MM/yyyy - HH:mm',//yyyy-MM-dd'T'HH:mm:ssZZZZZ
                    'model_timezone' => 'EUROPE/Paris',
                    'attr' => array(
//                        'class' => 'form-control input-inline datetimepicker datetime',
                        'class' => 'datetimepicker',
                        'data-date-format' => 'dd/MM/yyyy HH:mm',
                        'placeholder' => 'jj/mm/aaaa - hh:mm',
                    ),
                ))
            ->add('dateFin', DateTimeType::class,
                array(
                    'required' => true,
                    'widget' => 'single_text',
                    'format' => 'dd/MM/yyyy - HH:mm',//yyyy-MM-dd'T'HH:mm:ssZZZZZ
                    'model_timezone' => 'EUROPE/Paris',
                    'attr' => array(
//                        'class' => 'form-control input-inline datetimepicker datetime',
                        'class' => 'datetimepicker',
                        'data-date-format' => 'dd/MM/yyyy HH:mm',
//                        'placeholder' => 'format_date',
                        'placeholder' => 'jj/mm/aaaa - hh:mm',
                    )
                ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mondofute\Bundle\DecoteBundle\Entity\DecotePeriodeSejourDate'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'mondofute_bundle_decotebundle_decoteperiodesejourdate';
    }


}
