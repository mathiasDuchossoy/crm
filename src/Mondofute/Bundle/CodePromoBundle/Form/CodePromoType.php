<?php

namespace Mondofute\Bundle\CodePromoBundle\Form;

use HiDev\Bundle\CodePromoBundle\Entity\ClientAffectation;
use HiDev\Bundle\CodePromoBundle\Entity\TypeRemise;
use HiDev\Bundle\CodePromoBundle\Entity\Usage;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CodePromoType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $clients = $options['clients'];
        $builder
            ->add('libelle')
            ->add('clientAffectation', ChoiceType::class, array(
                'choices'       => array(
                    ClientAffectation::tous      => ClientAffectation::getLibelle(ClientAffectation::tous),
                    ClientAffectation::existants => ClientAffectation::getLibelle(ClientAffectation::existants),
                ),
                'placeholder'   => " --- Choisir l'affection --- ",
                'label'         => 'Affection',
                'attr'          => array(
                    'onchange'  => 'displayPanelClient(this);',
                )
            ))
            ->add('typeRemise', ChoiceType::class, array(
                'choices'       => array(
                    TypeRemise::euro       => TypeRemise::getLibelle(TypeRemise::euro),
                    TypeRemise::poucentage => TypeRemise::getLibelle(TypeRemise::poucentage),
                ),
                'placeholder'   => ' --- Choisir le type de remise --- ',
                'label'         => 'Type de remise'
            ))
            ->add('valeurRemise' , TextType::class)
            ->add('prixMini', TextType::class)
            ->add('usageCodePromo', ChoiceType::class, array(
                'choices'       => array(
                    Usage::unique           => Usage::getLibelle(Usage::unique),
                    Usage::uniqueParPeriode => Usage::getLibelle(Usage::uniqueParPeriode),
                    Usage::multiple         => Usage::getLibelle(Usage::multiple),
                ),
                'placeholder'   => " --- Choisir l'usage --- ",
                'label'         => 'Usage'
            ))
            ->add('actif')
            ->add('codePromoPeriodeValidates', CollectionType::class, array(
                    'entry_type' => 'HiDev\Bundle\CodePromoBundle\Form\CodePromoPeriodeValidateType',
                    'allow_add' => true,
                    'allow_delete' => true,
                    'label' => 'Périodes de validité',
                    'by_reference' => false,
                )
            )
            ->add('codePromoPeriodeSejours', CollectionType::class, array(
                    'entry_type' => 'Mondofute\Bundle\CodePromoBundle\Form\CodePromoPeriodeSejourType',
                    'allow_add' => true,
                    'allow_delete' => true,
                    'label' => 'Périodes de séjour',
                    'by_reference' => false,
                )
            )

            ->add('codePromoClients', CollectionType::class, array(
                    'entry_type' => 'Mondofute\Bundle\CodePromoBundle\Form\CodePromoClientType',
                    'allow_add' => true,
                    'allow_delete' => true,
                    'label' => " --- choisir un client ---",
                    'by_reference' => false,
                    'mapped'    => false
                )
            )
            ->add('site', HiddenType::class, array('mapped' => false))
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'    => 'Mondofute\Bundle\CodePromoBundle\Entity\CodePromo',
            'clients'       => array()
        ));
    }
}
