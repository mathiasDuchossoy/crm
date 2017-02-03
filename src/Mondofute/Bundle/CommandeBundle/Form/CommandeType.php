<?php

namespace Mondofute\Bundle\CommandeBundle\Form;

use Infinite\FormBundle\Form\Type\PolyCollectionType;
use Mondofute\Bundle\SiteBundle\Entity\Site;
use Mondofute\Bundle\SiteBundle\Repository\SiteRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommandeType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('site', EntityType::class, [
                'class' => Site::class,
                'required' => true,
                'query_builder' => function (SiteRepository $er) {
                    return $er->getSitesSansCrm();
                }
            ])
            ->add('dateCommande')
            ->add('numCommande')
            ->add('clients')
            ->add('commandeLignes', PolyCollectionType::class, array(
            'types' => array(
                CommandeLigneSejourType::class,
                CommandeLignePrestationAnnexeType::class,
                CommandeLigneFraisDossierType::class,
                CommandeLigneRemiseType::class,
                SejourNuiteType::class,
                SejourPeriodeType::class,
            ),
            'types_options' => array(
                CommandeLigneSejourType::class => array(// Here you can optionally define options for the InvoiceLineType
                ),
                CommandeLignePrestationAnnexeType::class => array(// Here you can optionally define options for the InvoiceProductLineType
                ),
                CommandeLigneFraisDossierType::class => array(// Here you can optionally define options for the InvoiceProductLineType
                ),
                CommandeLigneRemiseType::class => array(// Here you can optionally define options for the InvoiceProductLineType
                ),
                SejourNuiteType::class => array(// Here you can optionally define options for the InvoiceProductLineType
                ),
                SejourPeriodeType::class => array(// Here you can optionally define options for the InvoiceProductLineType
                )
            ),
            'allow_add' => true,
            'allow_delete' => true,
            'by_reference' => false
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mondofute\Bundle\CommandeBundle\Entity\Commande'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'mondofute_bundle_commandebundle_commande';
    }


}
