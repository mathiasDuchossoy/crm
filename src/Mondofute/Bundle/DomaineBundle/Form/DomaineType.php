<?php

namespace Mondofute\Bundle\DomaineBundle\Form;

use Mondofute\Bundle\DomaineBundle\Entity\Domaine;
use Mondofute\Bundle\DomaineBundle\Repository\DomaineRepository;
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
        $domaineUnifieId          = $options['domaineUnifieId'];
        $builder
            ->add('domaineParent' , EntityType::class , array(
                'class' => Domaine::class ,
                'placeholder' => '--- choisir un domaine parent ---',
                'required' => false ,
                'choice_label' => 'traductions[0].libelle',
                'query_builder' => function (DomaineRepository $rr) use ($locale, $domaineUnifieId) {
                    return $rr->getTraductionsDomainesByLocale($locale, $domaineUnifieId);
                },

            ))
            ->add('traductions', CollectionType::class, array(
                'entry_type' => DomaineTraductionType::class
            ))
//            ->add('domaineCarteIdentite' , 'Mondofute\Bundle\GeographieBundle\Form\DomaineCarteIdentiteType')
            ->add('site', HiddenType::class, array('mapped' => false));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mondofute\Bundle\DomaineBundle\Entity\Domaine',
            'locale' => 'fr_FR',
            'siteDomaineParent' => '',
            'domaineUnifieId' => null
        ));
    }
}