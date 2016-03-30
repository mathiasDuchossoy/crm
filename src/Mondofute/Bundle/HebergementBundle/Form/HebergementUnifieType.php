<?php

namespace Mondofute\Bundle\HebergementBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HebergementUnifieType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
//        dump();die;
//        $firstRegion = $builder->getData()->getDepartements()->First()->getRegion();
//        $siteRegion = (!empty($firstRegion)) ? $firstRegion->getSite() : null;
        $builder
            ->add('hebergements', CollectionType::class,
                array('entry_type' => HebergementType::class))
            ->add('fournisseurs', CollectionType::class,
                array(
                    'entry_type' => FournisseurHebergementType::class
                ,
                    'allow_add' => true
                ))
//            ->add('site')
//            ->add('regionUnifie')
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mondofute\Bundle\HebergementBundle\Entity\HebergementUnifie',
            'locale' => null,
        ));
    }
}
