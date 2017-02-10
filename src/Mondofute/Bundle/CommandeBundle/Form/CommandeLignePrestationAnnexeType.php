<?php

namespace Mondofute\Bundle\CommandeBundle\Form;

use Doctrine\Common\Collections\ArrayCollection;
use Mondofute\Bundle\CommandeBundle\Entity\CommandeLignePrestationAnnexe;
use Mondofute\Bundle\FournisseurBundle\Entity\Fournisseur;
use Mondofute\Bundle\FournisseurPrestationAffectationBundle\Entity\PrestationAnnexeLogement;
use Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity\FournisseurPrestationAnnexe;
use Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity\FournisseurPrestationAnnexeParam;
use Mondofute\Bundle\LogementBundle\Entity\Logement;
use Mondofute\Bundle\PrestationAnnexeBundle\Entity\FamillePrestationAnnexe;
use Mondofute\Bundle\PrestationAnnexeBundle\Repository\FamillePrestationAnnexeRepository;
use Mondofute\Bundle\StationBundle\Entity\Station;
use Mondofute\Bundle\StationBundle\Repository\StationRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommandeLignePrestationAnnexeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $locale = 'fr_FR';

        global $kernel;

        if ('AppCache' == get_class($kernel)) {
            $kernel = $kernel->getKernel();
        }
        $doctrine = $kernel->getContainer()->get('doctrine');

        $builder
            ->add('station', EntityType::class, [
                    'class' => Station::class,
                    'query_builder' => function (StationRepository $r) use ($locale) {
                        return $r->getTraductionsByLocale($locale, null, 1);
                    },
                    'empty_value' => ' --- Choisir une station --- ',
                    'mapped' => false,
                    'choice_label' => 'traductions[0].libelle'
                ]
            )
            ->add('famillePrestationAnnexe', EntityType::class, [
                    'class' => FamillePrestationAnnexe::class,
                    'query_builder' => function (FamillePrestationAnnexeRepository $r) use ($locale) {
                        return $r->getTraductionsByLocale($locale);
                    },
                    'empty_value' => ' --- Choisir un type --- ',
                    'mapped' => false,
                    'choice_label' => 'traductions[0].libelle'
                ]
            )
            ->add('fournisseur', EntityType::class, [
                    'class' => Fournisseur::class,
                    'empty_value' => ' --- Choisir un fournisseur --- ',
                    'mapped' => false,
                ]
            )
            ->add('dateDebut', DateType::class, array(
                'required' => true,
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
                'attr' => array(
                    'data-date-format' => 'dd/mm/yyyy',
                    'placeholder' => 'format_date',
                )
            ))
            ->add('dateFin', DateType::class, array(
                'required' => true,
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
                'attr' => array(
                    'data-date-format' => 'dd/mm/yyyy',
                    'placeholder' => 'format_date',
                )
            ))
            ->add('_type', HiddenType::class, array(
                'data' => 'prestationAnnexe', // Arbitrary, but must be distinct
                'mapped' => false
            ));

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($locale, $doctrine) {
            /** @var CommandeLignePrestationAnnexe $data */
            $data = $event->getData();
            $form = $event->getForm();
            if ($data && null !== $data->getId()) {
                /** @var PrestationAnnexeLogement $prestationAnnexeLogement */
                if (!empty($data->getCommandeLigneSejour())) {
                    $siteId = $data->getCommandeLigneSejour()->getCommande()->getSite()->getId();
//                    $periode = $data->getCommandeLigneSejour()->getPeriode();
                    $prestationAnnexeLogements = $data->getCommandeLigneSejour()->getLogement()->getFournisseurHebergement()->getLogements()->filter(function (Logement $element) use ($siteId) {
                        return $element->getSite()->getId() == $siteId;
                    })->first()->getPrestationAnnexeLogements();
                    $params = new ArrayCollection();
                    foreach ($prestationAnnexeLogements as $prestationAnnexeLogement) {
                        if ($prestationAnnexeLogement->getActif() && $prestationAnnexeLogement->getParam()->getFournisseurPrestationAnnexe()->getFournisseur() == $data->getCommandeLigneSejour()->getLogement()->getFournisseurHebergement()->getFournisseur()) {
                            $params->add($prestationAnnexeLogement->getParam());
                        }
                    }
                    $form
                        ->add('fournisseurPrestationAnnexeParam', EntityType::class, [
                            'class' => FournisseurPrestationAnnexeParam::class,
                            'required' => true,
                            'choices' => $params
                        ]);
                } else {
//                    $siteId = $data->getCommande()->getSite()->getId();
                    // récupérer les prestationAnnexe dont le fournisseur n'est pas affilié à la famille 'hébegement'

                    $fournisseurNotHebergements = $doctrine->getRepository(Fournisseur::class)->findByNotTypeHebergement();

                    $params = new ArrayCollection();
                    /** @var Fournisseur $fournisseurNotHebergement */
                    foreach ($fournisseurNotHebergements as $fournisseurNotHebergement) {
                        $fournisseurPrestationAnnexes = $fournisseurNotHebergement->getPrestationAnnexes();
                        /** @var FournisseurPrestationAnnexe $fournisseurPrestationAnnexe */
                        foreach ($fournisseurPrestationAnnexes as $fournisseurPrestationAnnexe) {
                            foreach ($fournisseurPrestationAnnexe->getParams() as $param) {
                                $params->add($param);
                            }
                        }
                    }

                    $form
                        ->add('fournisseurPrestationAnnexeParam', EntityType::class, [
                            'class' => FournisseurPrestationAnnexeParam::class,
                            'required' => true,
                            'choices' => $params,
                        ]);
                }
            } else {
                $form
                    ->add('fournisseurPrestationAnnexeParam', EntityType::class, [
                        'class' => FournisseurPrestationAnnexeParam::class,
//                        'choice_label' => 'traductions[0].nom',
                        'required' => true,
//                        'query_builder' => function (FournisseurPrestationAnnexeParamRepository $er)  {
//                            return $er->findByFournisseurNotHebergement();
//                        },
//                    'group_by' => 'famillePrestationAnnexe',
                        'empty_value' => ' --- Choisir une prestation annexe externe --- ',
//                        'choices' => new ArrayCollection(),
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
            'data_class' => 'Mondofute\Bundle\CommandeBundle\Entity\CommandeLignePrestationAnnexe',
            'model_class' => 'Mondofute\Bundle\CommandeBundle\Entity\CommandeLignePrestationAnnexe',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'mondofute_bundle_commandebundle_commandeligneprestationannexe';
    }


}
