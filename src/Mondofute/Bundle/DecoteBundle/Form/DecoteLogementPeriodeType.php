<?php

namespace Mondofute\Bundle\DecoteBundle\Form;

use Mondofute\Bundle\LogementBundle\Entity\Logement;
use Mondofute\Bundle\PeriodeBundle\Entity\Periode;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DecoteLogementPeriodeType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('logement', EntityType::class, [
                'class' => Logement::class,
                'property' => 'id'
            ])
            ->add('periode', EntityType::class, [
                'class' => Periode::class,
                'property' => 'id'
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mondofute\Bundle\DecoteBundle\Entity\DecoteLogementPeriode',
            'locale' => 'fr_FR'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'mondofute_bundle_decotebundle_decote_logement_periode';
    }


}
