<?php

namespace Mondofute\Bundle\RemiseClefBundle\Form;

use Mondofute\Bundle\LangueBundle\Entity\Langue;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RemiseClefTraductionType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('lieuxRemiseClef', null, array(
                'label' => 'lieux.remise.clef',
                'translation_domain' => 'messages'
            ))
//            ->add('remiseClef')
            ->add('langue', EntityType::class,
                array('class' => Langue::class, 'choice_label' => 'id', 'label' => 'langue'));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mondofute\Bundle\RemiseClefBundle\Entity\RemiseClefTraduction'
        ));
    }
}
