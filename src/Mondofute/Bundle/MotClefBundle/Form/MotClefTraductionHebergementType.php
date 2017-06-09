<?php

namespace Mondofute\Bundle\MotClefBundle\Form;

use Mondofute\Bundle\MotClefBundle\Entity\MotClefTraduction;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MotClefTraductionHebergementType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('classement')
            ->add('motClefTraduction', EntityType::class, [
                'class' => MotClefTraduction::class,
                "choice_label" => "id",
//                'multiple' => true,
//                'expanded'  => true,
//                'attr' => [
//                    'class' => 'js-mot-clef-multiple'
//                ],
                'required' => false,
            ])
;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mondofute\Bundle\MotClefBundle\Entity\MotClefTraductionHebergement'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'mondofute_bundle_motclefbundle_motcleftraductionhebergement';
    }


}
