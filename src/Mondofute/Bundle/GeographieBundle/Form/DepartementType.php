<?php

namespace Mondofute\Bundle\GeographieBundle\Form;

use Mondofute\Bundle\GeographieBundle\Entity\Region;
use Mondofute\Bundle\GeographieBundle\Repository\RegionRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
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
//        echo $this->id;
//        dump($idDomaine= $builder->getData()->getId());die;
        $locale = $options["locale"];
        $builder
            ->add('region', EntityType::class, array('class' => Region::class,
                'choice_label' => 'traductions[0].libelle',
                'query_builder' => function (RegionRepository $rr) use ($locale) {
                    return $rr->getTraductionsRegionsCRMByLocale($locale);
                },
            ))
            ->add('traductions', CollectionType::class, array(
                'entry_type' => DepartementTraductionType::class,
            ))
            ->add('site', HiddenType::class, array('mapped' => false))
        ;

    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mondofute\Bundle\GeographieBundle\Entity\Departement',
            'locale' => 'fr_FR'
        ));
    }
}
