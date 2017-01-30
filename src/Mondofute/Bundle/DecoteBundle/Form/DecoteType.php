<?php

namespace Mondofute\Bundle\DecoteBundle\Form;

use Mondofute\Bundle\DecoteBundle\Entity\ApplicationRemise;
use Mondofute\Bundle\DecoteBundle\Entity\CanalDecote;
use Mondofute\Bundle\DecoteBundle\Entity\ChoixVariante1;
use Mondofute\Bundle\DecoteBundle\Entity\Type;
use Mondofute\Bundle\DecoteBundle\Entity\TypeApplication;
use Mondofute\Bundle\DecoteBundle\Entity\TypePeriodeSejour;
use Mondofute\Bundle\DecoteBundle\Entity\TypePeriodeValidite;
use Mondofute\Bundle\DecoteBundle\Entity\TypeRemise;
use Mondofute\Bundle\DecoteBundle\Entity\Variante;
use Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity\PeriodeValidite;
use Mondofute\Bundle\PrestationAnnexeBundle\Entity\FamillePrestationAnnexe;
use Mondofute\Bundle\PrestationAnnexeBundle\Repository\FamillePrestationAnnexeRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DecoteType extends AbstractType
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
            ->add('type', ChoiceType::class, array(
                'choices' => array(
                    Type::visible => Type::getLibelle(Type::visible),
                    Type::masquee => Type::getLibelle(Type::masquee),
                ),
                'expanded' => true,
                'label' => 'Type'
            ))
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
                    TypePeriodeValidite::weekend => TypePeriodeValidite::getLibelle(TypePeriodeValidite::weekend),
                ),
                'placeholder' => " --- Choisir le typePeriodeValidite --- ",
                'label' => 'typePeriodeValidite'
            ))
            ->add('decotePeriodeValiditeDate', DecotePeriodeValiditeDateType::class, ['required' => false])
            ->add('decotePeriodeValiditeJour', DecotePeriodeValiditeJourType::class, ['required' => false])
            ->add('typePeriodeSejour', ChoiceType::class, array(
                'choices' => array(
                    TypePeriodeSejour::permanent => TypePeriodeSejour::getLibelle(TypePeriodeSejour::permanent),
                    TypePeriodeSejour::dateADate => TypePeriodeSejour::getLibelle(TypePeriodeSejour::dateADate),
                    TypePeriodeSejour::periode => TypePeriodeSejour::getLibelle(TypePeriodeSejour::periode),
                ),
                'placeholder' => " --- Choisir le typePeriodeSejour --- ",
                'label' => 'TypePeriodeSejour'
            ))
            ->add('decotePeriodeSejourDate', DecotePeriodeSejourDateType::class, ['required' => false])
            ->add('site', HiddenType::class, array('mapped' => false))
            ->add('decoteTypeAffectations', CollectionType::class, array(
                    'entry_type' => 'Mondofute\Bundle\DecoteBundle\Form\DecoteTypeAffectationType',
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
            ->add('decoteFournisseurs', CollectionType::class, array(
                    'entry_type' => 'Mondofute\Bundle\DecoteBundle\Form\DecoteFournisseurType',
                    'allow_add' => true,
                    'allow_delete' => true,
                    'label' => 'Decote fournisseurs',
                )
            )
            ->add('decoteHebergements', CollectionType::class, array(
                    'entry_type' => 'Mondofute\Bundle\DecoteBundle\Form\DecoteHebergementType',
                    'allow_add' => true,
                    'allow_delete' => true,
                    'label' => 'Decote hebergements',
                )
            )
            ->add('decoteFournisseurPrestationAnnexes', CollectionType::class, array(
                    'entry_type' => 'Mondofute\Bundle\DecoteBundle\Form\DecoteFournisseurPrestationAnnexeType',
                    'allow_add' => true,
                    'allow_delete' => true,
                    'label' => 'Decote fournisseur prestation annexes',
                )
            )
            ->add('decoteFamillePrestationAnnexes', CollectionType::class, array(
                    'entry_type' => 'Mondofute\Bundle\DecoteBundle\Form\DecoteFamillePrestationAnnexeType',
                    'allow_add' => true,
                    'allow_delete' => true,
                    'label' => 'Decote famille prestation annexes',
                )
            )
            ->add('decoteStations', CollectionType::class, array(
                    'entry_type' => DecoteStationType::class,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => false,
                    'label' => 'Decote stations',
                )
            )
            ->add('periodeValidites', EntityType::class, array(
                'class' => PeriodeValidite::class,
                'required' => true,
                'multiple' => true,
                'expanded' => true,
            ))
            ->add('logementPeriodes', CollectionType::class, array(
                'entry_type' => 'Mondofute\Bundle\DecoteBundle\Form\DecoteLogementPeriodeType',
                'allow_add' => true,
                'allow_delete' => true,
                'label' => 'Decote logement periode',
            ))
            ->add('canalDecotes', EntityType::class, array(
                'class' => CanalDecote::class,
                "choice_label" => "libelle",
                'required' => true,
                'multiple' => true,
                'expanded' => true))
            ->add('traductions', CollectionType::class, array(
                'entry_type' => DecoteTraductionType::class,
            ))
            ->add('variante', ChoiceType::class, array(
                'choices' => array(
                    Variante::sejour1Semainex2 => Variante::getLibelle(Variante::sejour1Semainex2),
                    Variante::aLaPersonne => Variante::getLibelle(Variante::aLaPersonne),
                    Variante::produitEnPack => Variante::getLibelle(Variante::produitEnPack),
                    Variante::venteFlash => Variante::getLibelle(Variante::venteFlash),
                    Variante::stockSpecifique => Variante::getLibelle(Variante::stockSpecifique),
                ),
                'required' => false,
                'placeholder' => " --- Choisir une variante --- ",
            ))
            ->add('choixVariante1', ChoiceType::class, array(
                'choices' => array(
                    ChoixVariante1::semaineMoinsChereOfferte => ChoixVariante1::getLibelle(ChoixVariante1::semaineMoinsChereOfferte),
                    ChoixVariante1::appliquerRemise => ChoixVariante1::getLibelle(ChoixVariante1::appliquerRemise),
                ),
                'expanded' => true,
                'required' => false,
                'empty_value' => false
            ))
            ->add('applicationRemise', ChoiceType::class, array(
                'choices' => array(
                    ApplicationRemise::deuxiemeSemaine => ApplicationRemise::getLibelle(ApplicationRemise::deuxiemeSemaine),
                    ApplicationRemise::semaineMoinsChere => ApplicationRemise::getLibelle(ApplicationRemise::semaineMoinsChere),
                ),
                'expanded' => true,
                'required' => false,
                'empty_value' => false
            ))
            ->add('compteARebours')
            ->add('stock');
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mondofute\Bundle\DecoteBundle\Entity\Decote',
            'locale' => 'fr_FR'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'mondofute_bundle_decotebundle_decote';
    }


}
