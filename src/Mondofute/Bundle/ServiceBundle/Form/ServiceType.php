<?php

namespace Mondofute\Bundle\ServiceBundle\Form;

use Mondofute\Bundle\ServiceBundle\Entity\CategorieService;
use Mondofute\Bundle\ServiceBundle\Entity\SousCategorieService;
use Mondofute\Bundle\ServiceBundle\Entity\TypeService;
use Mondofute\Bundle\ServiceBundle\Repository\TypeServiceRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ServiceType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $locale = $options["locale"];
        $builder
            ->add('defaut')
//            ->add('listeService')
            ->add('categorieService', EntityType::class, array(
                'class' => CategorieService::class,
                'choice_translation_domain' => 'messages',
//                'choice_label' => 'id',
            ))
            ->add('sousCategorieService', EntityType::class, array(
                'class' => SousCategorieService::class,
                'choice_translation_domain' => 'messages',
//                'choice_label' => 'id',
            ))
            ->add('type', EntityType::class, array(
//                'choice_label' => 'id',
                'class' => TypeService::class,
                'choice_label' => 'traductions[0].libelle',
                'query_builder' => function (TypeServiceRepository $ts) use ($locale) {
                    return $ts->getTraductionsByLocale($locale);
                },
            ))
            ->add('tarifs', CollectionType::class, array(
                'entry_type' => TarifServiceType::class,
                'allow_add' => true,
                'allow_delete' => true
            ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mondofute\Bundle\ServiceBundle\Entity\Service',
            'locale' => 'fr_FR',
        ));
    }

}
