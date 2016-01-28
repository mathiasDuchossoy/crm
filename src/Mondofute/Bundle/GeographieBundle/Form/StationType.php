<?php

namespace Mondofute\Bundle\GeographieBundle\Form;

use Mondofute\Bundle\GeographieBundle\Entity\ZoneTouristique;
use Mondofute\Bundle\GeographieBundle\Repository\ZoneTouristiqueRepository;
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
        $builder
            ->add('zoneTouristique', 'entity', array('class' => ZoneTouristique::class,
                "property" => "traductions[0].libelle",
                'query_builder' => function (ZoneTouristiqueRepository $rr) use ($locale) {
                    return $rr->getTraductionsZoneTouristiquesCRMByLocale($locale);
                },
            ))
            ->add('codePostal')
            ->add('moisOuverture')
            ->add('jourOuverture')
            ->add('moisFermeture')
            ->add('jourFermeture')
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
            'locale' => 'en'
        ));
    }
}
