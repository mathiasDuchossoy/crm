<?php

namespace Mondofute\Bundle\DomaineBundle\Form;

use Mondofute\Bundle\DomaineBundle\Entity\Domaine;
use Mondofute\Bundle\DomaineBundle\Repository\DomaineRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DomaineType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $locale = $options['locale'];
        $domaineUnifieId = $options['domaineUnifieId'];
        $builder
            ->add('domaineParent', EntityType::class, array(
                'class' => Domaine::class,
                'placeholder' => '--- choisir un domaine parent ---',
                'required' => false,
                'choice_label' => 'traductions[0].libelle',
                'query_builder' => function (DomaineRepository $rr) use ($locale, $domaineUnifieId) {
                    return $rr->getTraductionsDomainesByLocale($locale, $domaineUnifieId);
                },

            ))
            ->add('traductions', CollectionType::class, array(
                'entry_type' => DomaineTraductionType::class
            ))
            ->add('domaineCarteIdentite', DomaineCarteIdentiteType::class, array(
                'by_reference' => true
            ))
            ->add('site', HiddenType::class, array('mapped' => false))
            ->add('images', CollectionType::class, array(
                'entry_type' => DomaineImageType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
//                'required' => false,
            ))
            ->add('photos', CollectionType::class, array(
                'entry_type' => DomainePhotoType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
//                'required' => true,
            ))
            ->add('videos', CollectionType::class, array(
                'entry_type' => DomaineVideoType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
//                'required' => true,
            ))
            ->add('imagesParent')
            ->add('photosParent')
            ->add('videosParent')/*->add('modeleDescriptionForfaitSki', ModeleDescriptionForfaitSkiType::class, array(
                'data_class' => ModeleDescriptionForfaitSki::class
            ))*/
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mondofute\Bundle\DomaineBundle\Entity\Domaine',
            'locale' => 'fr_FR',
            'siteDomaineParent' => '',
            'domaineUnifieId' => null
        ));
    }
}
