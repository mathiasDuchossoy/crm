<?php

namespace Mondofute\Bundle\CommandeBundle\Form;

use Mondofute\Bundle\CommandeBundle\Entity\SejourPeriode;
use Mondofute\Bundle\HebergementBundle\Entity\FournisseurHebergement;
use Mondofute\Bundle\LogementBundle\Entity\Logement;
use Mondofute\Bundle\LogementBundle\Repository\LogementRepository;
use Mondofute\Bundle\PeriodeBundle\Entity\Periode;
use Mondofute\Bundle\PeriodeBundle\Repository\PeriodeRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SejourPeriodeType extends AbstractType
{

//    private $translator;
//
//    public function __construct(Translator $translator)
//    {
//        $this->translator = $translator;
//    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $locale = 'fr_FR';

        $builder
            ->add('dateAchat', DateTimeType::Class, [
                'attr' => [
                    'style' => 'display:none'
                ]
            ])
            ->add('prixCatalogue')
            ->add('prixVente', null, [
                'attr' => [
                    'class' => 'prixVente',
                    'onchange' => 'calculPrixVenteTotal();'
                ]
            ])
            ->add('quantite', null, [
                'attr' => [
                    'style' => 'display:none'
                ]
            ])
            ->add('prixAchat')
            ->add('nbParticipants')
            ->add('commandeLignePrestationAnnexes', CollectionType::class, array(
                'entry_type' => CommandeLignePrestationAnnexeType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype_name' => '__name_commande_ligne_prestation_annexe__'
            ))
            ->add('_type', HiddenType::class, array(
                'data' => 'sejourPeriode', // Arbitrary, but must be distinct
                'mapped' => false
            ))
            ->add('datePaiement', DateType::class, array(
                'required' => false,
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
                'attr' => array(
                    'data-date-format' => 'dd/mm/yyyy',
                    'placeholder' => 'format_date',
                    'class' => 'date',
                    'data-provide' => 'datepicker',
                )
            ));

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($locale, $options) {
            /** @var SejourPeriode $data */
            $data = $event->getData();
            /** @var FournisseurHebergement $fournisseurHebergement */
            $form = $event->getForm();
            if ($data && null !== $data->getId()) {
                $fournisseurHebergementId = $data->getLogement()->getFournisseurHebergement()->getId();
                $logementId = $data->getLogement()->getId();
                $siteId = $data->getCommande()->getSite()->getId();
                $periodeId = $data->getPeriode()->getId();
                $form
                    ->add('logement', EntityType::class, [
                        'class' => Logement::class,
                        'choice_label' => 'traductions[0].nom',
                        'required' => true,
                        'query_builder' => function (LogementRepository $er) use ($locale, $fournisseurHebergementId, $siteId) {
                            return $er->getByFournisseurHebergement($locale, $fournisseurHebergementId, $siteId);
                        },
                    ])
                    ->add('periode', EntityType::class, [
                        'class' => Periode::class,
                        'query_builder' => function (PeriodeRepository $er) use ($logementId, $periodeId) {
                            return $er->findPeriodeByLogementPrixNotEmpty($logementId, $periodeId);
                        },
                        'empty_value' => ' --- Choisir une période --- ',
                        'required' => true,
                    ]);
            } else if ($data && true === $options['addSejourPeriode']) {
                $fournisseurHebergementId = $data->getLogement()->getFournisseurHebergement()->getId();
                $siteId = $data->getLogement()->getSite()->getId();
                $logementId = $data->getLogement()->getId();
                $periodeId = $data->getPeriode()->getId();
                $form
                    ->add('logement', EntityType::class, [
                        'class' => Logement::class,
                        'choice_label' => 'traductions[0].nom',
                        'required' => true,
                        'query_builder' => function (LogementRepository $er) use ($locale, $fournisseurHebergementId, $siteId) {
                            return $er->getByFournisseurHebergement($locale, $fournisseurHebergementId, $siteId);
                        },
                    ])
                    ->add('periode', EntityType::class, [
                        'class' => Periode::class,
                        'query_builder' => function (PeriodeRepository $er) use ($logementId, $periodeId) {
                            return $er->findPeriodeByLogementPrixNotEmpty($logementId, $periodeId);
                        },
                        'empty_value' => ' --- Choisir une période --- ',
                        'required' => true,
                    ]);
            } else {
                $form
                    ->add('logement', EntityType::class, [
                        'class' => Logement::class,
                        'choice_label' => 'id',
                        'required' => true
                    ])
                    ->add('periode', EntityType::class, [
                        'class' => Periode::class,
                    ]);
            }

        });
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mondofute\Bundle\CommandeBundle\Entity\SejourPeriode',
            'model_class' => 'Mondofute\Bundle\CommandeBundle\Entity\SejourPeriode',
            'addSejourPeriode' => false
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'mondofute_bundle_commandebundle_sejourperiode';
    }


}
