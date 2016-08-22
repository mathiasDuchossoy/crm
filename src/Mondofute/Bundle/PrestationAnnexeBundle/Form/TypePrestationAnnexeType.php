<?php

namespace Mondofute\Bundle\PrestationAnnexeBundle\Form;

use Mondofute\Bundle\PrestationAnnexeBundle\Entity\SousTypePrestationAnnexe;
use Mondofute\Bundle\PrestationAnnexeBundle\Entity\TypePrestationAnnexeTraduction;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TypePrestationAnnexeType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('traductions', CollectionType::class, array(
                'entry_type' => TypePrestationAnnexeTraductionType::class,
            ))
            ->add('sousTypePrestationAnnexes', CollectionType::class, array(
                'entry_type' => SousTypePrestationAnnexeType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
            ))
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mondofute\Bundle\PrestationAnnexeBundle\Entity\TypePrestationAnnexe'
        ));
    }
}
