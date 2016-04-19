<?php

namespace Mondofute\Bundle\HebergementBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TypeHebergementUnifieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('typeHebergements', CollectionType::class, array(
                'entry_type' => TypeHebergementType::class,
                'entry_options' => array('locale' => $options["locale"])
            ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mondofute\Bundle\HebergementBundle\Entity\TypeHebergementUnifie'
        ));
    }
}
