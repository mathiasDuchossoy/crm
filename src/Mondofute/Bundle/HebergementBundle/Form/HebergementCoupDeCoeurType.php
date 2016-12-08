<?php

namespace Mondofute\Bundle\HebergementBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HebergementCoupDeCoeurType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('dateHeureDebut', DateTimeType::class,
                array(
                    'required' => true,
                    'widget' => 'single_text',
                    'format' => 'dd/MM/yyyy - HH:mm',//yyyy-MM-dd'T'HH:mm:ssZZZZZ
                    'model_timezone' => 'EUROPE/Paris',
                    'attr' => array(
                        'class' => 'datetimepicker date-debut',
                        'data-date-format' => 'dd/MM/yyyy HH:mm',
                        'placeholder' => 'jj/mm/aaaa - hh:mm',
                    ),
                ))
            ->add('dateHeureFin', DateTimeType::class,
                array(
//                    'required' => true,
                    'widget' => 'single_text',
                    'format' => 'dd/MM/yyyy - HH:mm',//yyyy-MM-dd'T'HH:mm:ssZZZZZ
                    'model_timezone' => 'EUROPE/Paris',
                    'attr' => array(
                        'class' => 'datetimepicker',
                        'data-date-format' => 'dd/MM/yyyy HH:mm',
                        'placeholder' => 'jj/mm/aaaa - hh:mm',
                    ),
                ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mondofute\Bundle\HebergementBundle\Entity\HebergementCoupDeCoeur'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'mondofute_bundle_hebergementbundle_hebergementcoupdecoeur';
    }


}
