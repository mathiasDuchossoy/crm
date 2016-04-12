<?php

namespace Mondofute\Bundle\StationBundle\Form;

use Mondofute\Bundle\DomaineBundle\Entity\Domaine;
use Mondofute\Bundle\DomaineBundle\Repository\DomaineRepository;
use Mondofute\Bundle\GeographieBundle\Entity\Departement;
use Mondofute\Bundle\GeographieBundle\Entity\Profil;
use Mondofute\Bundle\GeographieBundle\Entity\Secteur;
use Mondofute\Bundle\GeographieBundle\Entity\ZoneTouristique;
use Mondofute\Bundle\GeographieBundle\Repository\DepartementRepository;
use Mondofute\Bundle\GeographieBundle\Repository\ProfilRepository;
use Mondofute\Bundle\GeographieBundle\Repository\SecteurRepository;
use Mondofute\Bundle\GeographieBundle\Repository\ZoneTouristiqueRepository;
use Mondofute\Bundle\StationBundle\Entity\Station;
use Mondofute\Bundle\StationBundle\Repository\StationRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StationType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $locale = $options["locale"];
        $stationUnifieId = $options['stationUnifieId'];
        $builder
            ->add('stationMere', EntityType::class, array(
                'class' => Station::class,
                'placeholder' => '--- choisir une station mère ---',
                'required' => false,
                'choice_label' => 'traductions[0].libelle',
                'query_builder' => function (StationRepository $r) use ($locale, $stationUnifieId) {
                    return $r->getTraductionsByLocale($locale, $stationUnifieId);
                },

            ))
            ->add('zoneTouristiques', EntityType::class, array('class' => ZoneTouristique::class,
                'required' => false,
                "choice_label" => "traductions[0].libelle",
                "placeholder" => " --- choisir une zone touristique ---",
                'query_builder' => function (ZoneTouristiqueRepository $rr) use ($locale) {
                    return $rr->getTraductionsZoneTouristiquesByLocale($locale);
                },
                'multiple' => true
            ))
//            ->add('secteur', EntityType::class, array(
//                'class' => Secteur::class,
//                'required' => false,
//                "choice_label" => "traductions[0].libelle",
//                "placeholder" => " --- choisir un secteur ---",
//                'query_builder' => function (SecteurRepository $rr) use ($locale) {
//                    return $rr->getTraductionsByLocale($locale);
//                },
//            ))

            ->add('secteurs', EntityType::class, array(
                'class' => Secteur::class,
                'required' => false,
                "choice_label" => "traductions[0].libelle",
                "placeholder" => " --- choisir un secteur ---",
                'query_builder' => function (SecteurRepository $rr) use ($locale) {
                    return $rr->getTraductionsByLocale($locale);
                },
                'multiple' => true
            ))
            ->add('domaine', EntityType::class, array(
                'class' => Domaine::class,
                'required' => false,
                "choice_label" => "traductions[0].libelle",
                "placeholder" => " --- choisir un domaine ---",
                'query_builder' => function (DomaineRepository $rr) use ($locale) {
                    return $rr->getTraductionsByLocale($locale);
                },
                'attr' => array(
                    'onchange' => 'javascript:sortStationByDomaine(this);'
                )
            ))
            ->add('profils', EntityType::class, array(
                'class' => Profil::class,
                'required' => false,
                "choice_label" => "traductions[0].libelle",
                "placeholder" => " --- choisir un secteur ---",
                'query_builder' => function (ProfilRepository $rr) use ($locale) {
                    return $rr->getTraductionsByLocale($locale);
                },
                'multiple' => true
            ))
            ->add('departement', EntityType::class, array(
                'class' => Departement::class,
                'required' => true,
                "choice_label" => "traductions[0].libelle",
                "placeholder" => " --- choisir un département ---",
                'query_builder' => function (DepartementRepository $rr) use ($locale) {
                    return $rr->getTraductionsByLocale($locale);
                },
            ))
            ->add('traductions', CollectionType::class, array(
                'entry_type' => StationTraductionType::class,
            ))
            ->add('site', HiddenType::class, array('mapped' => false))
//            ->add('stationCommentVenirUnifie', StationCommentVenirUnifieType::class , array('auto_initialize' => false))
//            ->add('stationCommentVenir', StationCommentVenirType::class , array('auto_initialize' => false))
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mondofute\Bundle\StationBundle\Entity\Station',
            'locale' => 'fr_FR',
            'stationUnifieId' => null
        ));
    }
}
