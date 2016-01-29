<?php

namespace Mondofute\Bundle\GeographieBundle\Form;

use Mondofute\Bundle\GeographieBundle\Entity\Domaine;
use Mondofute\Bundle\GeographieBundle\Repository\DomaineRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DomaineType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $locale             = $options['locale'];
        $siteDomaineParent  = $options['siteDomaineParent'];
        $domaineUnifieId          = $options['domaineUnifieId'];
        $builder
            ->add('domaineParent' , EntityType::class , array(
                'class' => Domaine::class ,
                'empty_value' => '--- choisir un domaine parent ---',
                'required' => false ,
                'property' => 'traductions[0].libelle',
                'query_builder' => function (DomaineRepository $rr) use ($locale , $siteDomaineParent , $domaineUnifieId) {
                    return $rr->getTraductionsDomainesCRMByLocale($locale , $siteDomaineParent , $domaineUnifieId);
                },

            ))
            ->add('traductions', CollectionType::class, array(
                'entry_type' => DomaineTraductionType::class,
                'required' => false,
            ))
            ->add('site', HiddenType::class, array('mapped' => false));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mondofute\Bundle\GeographieBundle\Entity\Domaine',
            'locale' => 'fr_FR',
            'siteDomaineParent' => '',
            'domaineUnifieId' => null
        ));
    }
}
