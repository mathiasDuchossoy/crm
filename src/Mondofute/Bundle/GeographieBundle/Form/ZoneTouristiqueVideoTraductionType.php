<?php

namespace Mondofute\Bundle\GeographieBundle\Form;

use Mondofute\Bundle\LangueBundle\Entity\Langue;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ZoneTouristiqueVideoTraductionType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('libelle', TextType::class, array(
                'required' => true
            ))
            ->add('langue', EntityType::class, array(
                'class' => Langue::class,
                'choice_label' => 'id',
                'label_attr' => [
                    'style' => 'display:none',
                ],
                'attr' => [
                    'style' => 'display:none',
                ],
            ));
    }


    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mondofute\Bundle\GeographieBundle\Entity\ZoneTouristiqueVideoTraduction'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'mondofute_bundle_geographiebundle_zonetouristiquevideotraduction';
    }


}
