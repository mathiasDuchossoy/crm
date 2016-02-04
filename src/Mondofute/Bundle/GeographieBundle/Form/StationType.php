<?php

namespace Mondofute\Bundle\GeographieBundle\Form;

use Mondofute\Bundle\GeographieBundle\Entity\ZoneTouristique;
use Mondofute\Bundle\GeographieBundle\Repository\ZoneTouristiqueRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Date;

class StationType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $locale = $options["locale"];
        $siteZoneTouristique = $options["siteZoneTouristique"];
        $builder
            ->add('zoneTouristique', EntityType::class, array('class' => ZoneTouristique::class,
                "choice_label" => "traductions[0].libelle",
                "placeholder" => " --- choisir une zone touristique ---",
                'query_builder' => function (ZoneTouristiqueRepository $rr) use ($locale, $siteZoneTouristique) {
                    return $rr->getTraductionsZoneTouristiquesCRMByLocale($locale, $siteZoneTouristique);
                },
            ))
            ->add('codePostal')
            ->add('jourOuverture')
            ->add('moisOuverture')
            ->add('jourFermeture')
            ->add('moisFermeture')
            ->add('lienMeteo')
            ->add('traductions', CollectionType::class, array(
                'entry_type' => StationTraductionType::class,
            ))
            ->add('site', HiddenType::class, array('mapped' => false));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mondofute\Bundle\GeographieBundle\Entity\Station',
            'locale' => 'fr_FR',
            'siteZoneTouristique' => null
        ));
    }
}
