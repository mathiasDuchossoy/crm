<?php

namespace Mondofute\Bundle\CommandeBundle\Form;

use Mondofute\Bundle\CommandeBundle\Entity\SejourNuite;
use Mondofute\Bundle\HebergementBundle\Entity\FournisseurHebergement;
use Mondofute\Bundle\LogementBundle\Entity\Logement;
use Mondofute\Bundle\LogementBundle\Repository\LogementRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SejourNuiteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $locale = 'fr_FR';

        $builder
            ->add('prixVente')
            ->add('commandeLignePrestationAnnexes', CollectionType::class, array(
                'entry_type' => CommandeLignePrestationAnnexeType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype_name' => '__name_commande_ligne_prestation_annexe__'
            ))
            ->add('_type', HiddenType::class, array(
                'data' => 'sejourNuite', // Arbitrary, but must be distinct
                'mapped' => false
            ));

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($locale) {
            /** @var SejourNuite $data */
            $data = $event->getData();
            /** @var FournisseurHebergement $fournisseurHebergement */
            $form = $event->getForm();
            if ($data && null !== $data->getId()) {
                $fournisseurHebergementId = $data->getLogement()->getFournisseurHebergement()->getId();
                $siteId = $data->getCommande()->getSite()->getId();
                $form
                    ->add('logement', EntityType::class, [
                        'class' => Logement::class,
                        'choice_label' => 'traductions[0].nom',
                        'required' => true,
                        'query_builder' => function (LogementRepository $er) use ($locale, $fournisseurHebergementId, $siteId) {
                            return $er->getByFournisseurHebergement($locale, $fournisseurHebergementId, $siteId);
                        },
//                        'choices' => $fournisseurHebergement->getLogements()
                    ]);
            } else {
                $form
                    ->add('logement', EntityType::class, [
                        'class' => Logement::class,
                        'choice_label' => 'id',
                        'required' => true,
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
            'data_class' => 'Mondofute\Bundle\CommandeBundle\Entity\SejourNuite',
            'model_class' => 'Mondofute\Bundle\CommandeBundle\Entity\SejourNuite'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'mondofute_bundle_commandebundle_sejournuite';
    }


}
