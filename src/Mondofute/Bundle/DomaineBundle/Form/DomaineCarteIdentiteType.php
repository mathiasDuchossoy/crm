<?php

namespace Mondofute\Bundle\DomaineBundle\Form;

use Mondofute\Bundle\DomaineBundle\Entity\NiveauSkieur;
use Mondofute\Bundle\DomaineBundle\Repository\NiveauSkieurRepository;
use Mondofute\Bundle\UniteBundle\Form\DistanceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DomaineCarteIdentiteType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $locale = $options['locale'];
        $builder
            ->add('altitudeMini', DistanceType::class)
            ->add('altitudeMaxi', DistanceType::class)
            ->add('kmPistesSkiAlpin', KmPistesAlpinType::class)
            ->add('kmPistesSkiNordique', KmPistesNordiqueType::class)
            ->add('traductions', CollectionType::class, array(
                'entry_type' => DomaineCarteIdentiteTraductionType::class
            ))
            ->add('site', HiddenType::class, array('mapped' => false))
            ->add('snowpark', SnowparkType::class, array('required' => false,))
            ->add('handiski', HandiskiType::class, array('required' => false,))
            ->add('remonteeMecanique', RemonteeMecaniqueType::class, array('attr' => array('min' => 0)))
            ->add('niveauSkieur', EntityType::class, array(
                'class' => NiveauSkieur::class,
                'placeholder' => '--- choisir un niveau de skieur ---',
//                'required' => false,
                'choice_label' => 'traductions[0].libelle',
                'query_builder' => function (NiveauSkieurRepository $rr) use ($locale) {
                    return $rr->getTraductionsByLocale($locale);
                },

            ))
            ->add('pistes', CollectionType::class, array(
                'entry_type' => PisteType::class
            ))
            ->add('images', CollectionType::class, array(
                'entry_type' => DomaineCarteIdentiteImageType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
//                'required' => false,
            ))
            ->add('photos', CollectionType::class, array(
                'entry_type' => DomaineCarteIdentitePhotoType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
//                'required' => true,
            ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mondofute\Bundle\DomaineBundle\Entity\DomaineCarteIdentite',
            'locale' => 'fr_FR',
        ));
    }

    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        /** @var FormView $viewChild */
        $entities = array('altitudeMini', 'altitudeMaxi');
        foreach ($entities as $entity) {
            foreach ($view->children[$entity]->children as $child) {
                $child->vars['attr'] = array('data-unique_block_prefix' => $child->vars['unique_block_prefix']);
            }
        }
    }
}
