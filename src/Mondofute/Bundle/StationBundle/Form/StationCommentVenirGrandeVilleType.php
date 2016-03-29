<?php

namespace Mondofute\Bundle\StationBundle\Form;

use Mondofute\Bundle\GeographieBundle\Entity\GrandeVille;
use Mondofute\Bundle\GeographieBundle\Repository\GrandeVilleRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StationCommentVenirGrandeVilleType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $locale = $options["locale"];
        $builder
//            ->add('stationCommentVenir')
//            ->add('grandeVille')
            ->add('grandeVille', EntityType::class, array(
                'class' => GrandeVille::class,
                'required' => false,
                "choice_label" => "traductions[0].libelle",
                "placeholder" => " --- choisir un domaine ---",
                'query_builder' => function (GrandeVilleRepository $rr) use ($locale) {
                    return $rr->getTraductionsByLocale($locale);
                },
                'label' => false
            ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mondofute\Bundle\StationBundle\Entity\StationCommentVenirGrandeVille',
            'locale' => 'fr_FR',
        ));
    }
}
