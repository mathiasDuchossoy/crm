<?php

namespace Mondofute\Bundle\GeographieBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DomaineUnifieType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $domaineUnifieId  =$builder->getData()->getId();
        $firstDomaineParent = $builder->getData()->getDomaines()->First()->getDomaineParent();
        $siteDomaineParent = (!empty($firstDomaineParent)) ? $firstDomaineParent->getSite() : null;
        $builder
            ->add('domaines', CollectionType::class, array(
                'entry_type' => DomaineType::class ,
                'entry_options' => array(
                    'locale' => $options['locale'] ,
                    'siteDomaineParent' => $siteDomaineParent,
                    'domaineUnifieId' => $domaineUnifieId
                )
            )
            );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mondofute\Bundle\GeographieBundle\Entity\DomaineUnifie',
            'locale' => 'fr_FR'
        ));
    }
}
