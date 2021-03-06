<?php

namespace Mondofute\Bundle\HebergementBundle\Form;

use Mondofute\Bundle\LangueBundle\Entity\Langue;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FournisseurHebergementTraductionType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('acces')
            ->add('langue', EntityType::class,
                array(
                    'class' => Langue::class,
                    'choice_label' => 'id',
                    'attr' => array('style' => 'display:none;'),
                    'label_attr' => array('style' => 'display:none')
                ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {

        $resolver->setDefaults(array(
            'data_class' => 'Mondofute\Bundle\HebergementBundle\Entity\FournisseurHebergementTraduction'
        ));
    }
}
