<?php

namespace Mondofute\Bundle\GeographieBundle\Form;

use Mondofute\Bundle\GeographieBundle\Entity\Region;
use Mondofute\Bundle\GeographieBundle\Repository\RegionRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DepartementType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $locale = $options["locale"];
        $builder
            ->add('region', 'entity', array('class' => Region::class,
                "property" => "traductions[0].libelle",
                'query_builder' => function (RegionRepository $rr) use ($locale) {
                    return $rr->getTraductionsRegionsCRMByLocale($locale);
                },
            ))
            ->add('traductions', CollectionType::class, array(
                'entry_type' => DepartementTraductionType::class,
                'required' => false,
            ))
            ->add('site', HiddenType::class, array('mapped' => false))//            ->add('regionUnifie')
        ;

    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mondofute\Bundle\GeographieBundle\Entity\Departement',
            'locale' => 'en'
        ));
    }
}
