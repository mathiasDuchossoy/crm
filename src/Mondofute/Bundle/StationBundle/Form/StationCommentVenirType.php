<?php

namespace Mondofute\Bundle\StationBundle\Form;

use Mondofute\Bundle\GeographieBundle\Entity\GrandeVille;
use Mondofute\Bundle\GeographieBundle\Repository\GrandeVilleRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StationCommentVenirType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $locale = $options["locale"];
//        echo $locale = $options["locale"];
//        die;
        $builder
            ->add('site', HiddenType::class, array('mapped' => false))
            ->add('traductions', CollectionType::class, array(
                'entry_type' => StationCommentVenirTraductionType::class
            ))
            ->add('grandeVilles', EntityType::class, array(
                'class' => GrandeVille::class,
                'required' => true,
                "choice_label" => "traductions[0].libelle",
                "placeholder" => " --- choisir un type ---",
                'query_builder' => function (GrandeVilleRepository $r) use ($locale) {
                    return $r->getTraductionsByLocale($locale);
                },
                'multiple' => true,
                'expanded' => true
            ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mondofute\Bundle\StationBundle\Entity\StationCommentVenir',
            'locale' => 'fr_FR'
        ));
    }
}
