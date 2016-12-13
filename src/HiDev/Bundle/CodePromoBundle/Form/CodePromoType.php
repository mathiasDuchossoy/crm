<?php

namespace HiDev\Bundle\CodePromoBundle\Form;

use HiDev\Bundle\CodePromoBundle\Entity\ClientAffectation;
use HiDev\Bundle\CodePromoBundle\Entity\TypeRemise;
use HiDev\Bundle\CodePromoBundle\Entity\Usage;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
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
        $builder
            ->add('libelle')
            ->add('code')
            ->add('clientAffectation', ChoiceType::class, array(
                'choices' => array(
                    ClientAffectation::tous => ClientAffectation::getLibelle(ClientAffectation::tous),
                    ClientAffectation::existants => ClientAffectation::getLibelle(ClientAffectation::existants),
                ),
                'placeholder' => " --- Choisir l'affection --- ",
                'label' => 'Affection'
            ))
            ->add('typeRemise', ChoiceType::class, array(
                'choices' => array(
                    TypeRemise::euro => TypeRemise::getLibelle(TypeRemise::euro),
                    TypeRemise::poucentage => TypeRemise::getLibelle(TypeRemise::poucentage),
                ),
                'placeholder' => ' --- Choisir le type de remise --- ',
                'label' => 'Type de remise'
            ))
            ->add('valeurRemise')
            ->add('prixMini')
            ->add('usage', ChoiceType::class, array(
                'choices' => array(
                    Usage::unique => Usage::getLibelle(Usage::unique),
                    Usage::uniqueParPeriode => Usage::getLibelle(Usage::uniqueParPeriode),
                    Usage::multiple => Usage::getLibelle(Usage::multiple),
                ),
                'placeholder' => " --- Choisir l'usage --- ",
                'label' => 'Usage'
            ))
            ->add('actif')
            ->add('codePromoPeriodeValidites', CollectionType::class, array(
                    'entry_type' => 'HiDev\Bundle\CodePromoBundle\Form\CodePromoPeriodeValiditeType',
                    'allow_add' => true,
                    'allow_delete' => true,
                    'label' => 'Périodes de validité',
                    'by_reference' => false,
                )
            );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'HiDev\Bundle\CodePromoBundle\Entity\CodePromo'
        ));
    }
}
