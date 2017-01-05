<?php

namespace Mondofute\Bundle\PromotionBundle\Form;

use Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity\PeriodeValidite;
use Mondofute\Bundle\PrestationAnnexeBundle\Entity\FamillePrestationAnnexe;
use Mondofute\Bundle\PrestationAnnexeBundle\Repository\FamillePrestationAnnexeRepository;
use Mondofute\Bundle\PromotionBundle\Entity\TypeApplication;
use Mondofute\Bundle\PromotionBundle\Entity\TypePeriodeSejour;
use Mondofute\Bundle\PromotionBundle\Entity\TypePeriodeValidite;
use Mondofute\Bundle\PromotionBundle\Entity\TypeRemise;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PromotionType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $locale = $options["locale"];
        $builder
            ->add('libelle')
            ->add('typeRemise', ChoiceType::class, array(
                'choices' => array(
                    TypeRemise::euro => TypeRemise::getLibelle(TypeRemise::euro),
                    TypeRemise::poucentage => TypeRemise::getLibelle(TypeRemise::poucentage),
                ),
                'placeholder' => ' --- Choisir le type de remise --- ',
                'label' => 'Type de remise'
            ))
            ->add('valeurRemise', TextType::class)
            ->add('typePeriodeValidite', ChoiceType::class, array(
                'choices' => array(
                    TypePeriodeValidite::permanent => TypePeriodeValidite::getLibelle(TypePeriodeValidite::permanent),
                    TypePeriodeValidite::dateADate => TypePeriodeValidite::getLibelle(TypePeriodeValidite::dateADate),
                    TypePeriodeValidite::periode => TypePeriodeValidite::getLibelle(TypePeriodeValidite::periode),
                ),
                'placeholder' => " --- Choisir le typePeriodeValidite --- ",
                'label' => 'typePeriodeValidite'
            ))
            ->add('promotionPeriodeValiditeDate', PromotionPeriodeValiditeDateType::class, ['required' => false])
            ->add('promotionPeriodeValiditeJour', PromotionPeriodeValiditeJourType::class, ['required' => false])
            ->add('typePeriodeSejour', ChoiceType::class, array(
                'choices' => array(
                    TypePeriodeSejour::permanent => TypePeriodeSejour::getLibelle(TypePeriodeSejour::permanent),
                    TypePeriodeSejour::dateADate => TypePeriodeSejour::getLibelle(TypePeriodeSejour::dateADate),
                    TypePeriodeSejour::periode => TypePeriodeSejour::getLibelle(TypePeriodeSejour::periode),
                ),
                'placeholder' => " --- Choisir le typePeriodeSejour --- ",
                'label' => 'TypePeriodeSejour'
            ))
            ->add('promotionPeriodeSejourDate', PromotionPeriodeSejourDateType::class, ['required' => false])
            ->add('site', HiddenType::class, array('mapped' => false))
            ->add('promotionTypeAffectations', CollectionType::class, array(
                    'entry_type' => 'Mondofute\Bundle\PromotionBundle\Form\PromotionTypeAffectationType',
                    'allow_add' => true,
                    'allow_delete' => true,
                    'label' => 'Affectations',
                    'by_reference' => true
                )
            )
            ->add('typeApplication', ChoiceType::class, array(
                'choices' => array(
                    TypeApplication::prixLogement => TypeApplication::getLibelle(TypeApplication::prixLogement),
                    TypeApplication::ALaPersonne => TypeApplication::getLibelle(TypeApplication::ALaPersonne),
                ),
                'placeholder' => " --- Choisir le typeApplication --- ",
                'label' => 'TypeApplication'
            ))
            ->add('typeFournisseurs', EntityType::class, array(
                'class' => FamillePrestationAnnexe::class,
                'required' => true,
                "choice_label" => "traductions[0].libelle",
                "placeholder" => " --- choisir un type ---",
                'query_builder' => function (FamillePrestationAnnexeRepository $r) use ($locale) {
                    return $r->getTraductionsByLocale($locale);
                },
                'multiple' => true,
                'expanded' => true,
            ))
            ->add('promotionFournisseurs', CollectionType::class, array(
                    'entry_type' => 'Mondofute\Bundle\PromotionBundle\Form\PromotionFournisseurType',
                    'allow_add' => true,
                    'allow_delete' => true,
                    'label' => 'Promotion fournisseurs',
                )
            )
            ->add('promotionHebergements', CollectionType::class, array(
                    'entry_type' => 'Mondofute\Bundle\PromotionBundle\Form\PromotionHebergementType',
                    'allow_add' => true,
                    'allow_delete' => true,
                    'label' => 'Promotion hebergements',
                )
            )
            ->add('promotionFournisseurPrestationAnnexes', CollectionType::class, array(
                    'entry_type' => 'Mondofute\Bundle\PromotionBundle\Form\PromotionFournisseurPrestationAnnexeType',
                    'allow_add' => true,
                    'allow_delete' => true,
                    'label' => 'Promotion fournisseur prestation annexes',
                )
            )
            ->add('promotionFamillePrestationAnnexes', CollectionType::class, array(
                    'entry_type' => 'Mondofute\Bundle\PromotionBundle\Form\PromotionFamillePrestationAnnexeType',
                    'allow_add' => true,
                    'allow_delete' => true,
                    'label' => 'Promotion famille prestation annexes',
                )
            )
            ->add('promotionStations', CollectionType::class, array(
                    'entry_type' => PromotionStationType::class,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => false,
                    'label' => 'Promotion stations',
                )
            )
            ->add('periodeValidites', EntityType::class, array(
                'class' => PeriodeValidite::class,
                'required' => true,
//                "choice_label" => "traductions[0].libelle",
//                "placeholder" => " --- choisir un type ---",
//                'query_builder' => function (FamillePrestationAnnexeRepository $r) use ($locale) {
//                    return $r->getTraductionsByLocale($locale);
//                },
                'multiple' => true,
                'expanded' => true,
            ))
            ->add('logementPeriodes', CollectionType::class, array(
                'entry_type' => 'Mondofute\Bundle\PromotionBundle\Form\PromotionLogementPeriodeType',
                'allow_add' => true,
                'allow_delete' => true,
                'label' => 'Promotion logement periode',
            ))
            ->add('traductions', CollectionType::class, array(
                'entry_type' => PromotionTraductionType::class,
            ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mondofute\Bundle\PromotionBundle\Entity\Promotion',
            'locale' => 'fr_FR'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'mondofute_bundle_promotionbundle_promotion';
    }


}
