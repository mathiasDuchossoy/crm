<?php

namespace Mondofute\Bundle\GeographieBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DomaineCarteIdentiteUnifieType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('domaineCarteIdentites', CollectionType::class, array('entry_type' => DomaineCarteIdentiteType::class));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mondofute\Bundle\GeographieBundle\Entity\DomaineCarteIdentiteUnifie',
            'locale' => 'fr_FR'
        ));
    }
}
