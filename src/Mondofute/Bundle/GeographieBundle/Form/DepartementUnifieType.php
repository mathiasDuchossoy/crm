<?php

namespace Mondofute\Bundle\GeographieBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DepartementUnifieType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
//        dump();die;
        $firstRegion = $builder->getData()->getDepartements()->First()->getRegion();
        $siteRegion = (!empty($firstRegion)) ? $firstRegion->getSite() : null;
        $builder
            ->add('departements', CollectionType::class, array('entry_type' => DepartementType::class, 'entry_options' => array('locale' => $options["locale"], 'siteRegion' => $siteRegion)))
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
            'data_class' => 'Mondofute\Bundle\GeographieBundle\Entity\DepartementUnifie',
            'locale' => 'fr_FR'
        ));
    }
}
