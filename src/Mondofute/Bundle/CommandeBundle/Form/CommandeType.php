<?php

namespace Mondofute\Bundle\CommandeBundle\Form;

use Infinite\FormBundle\Form\Type\PolyCollectionType;
use Mondofute\Bundle\ClientBundle\Entity\Client;
use Mondofute\Bundle\ClientBundle\Form\ClientType;
use Mondofute\Bundle\CommandeBundle\Entity\LitigeDossier;
use Mondofute\Bundle\CommandeBundle\Entity\StatutDossier;
use Mondofute\Bundle\CommandeBundle\Repository\LitigeDossierRepository;
use Mondofute\Bundle\CommandeBundle\Repository\StatutDossierRepository;
use Mondofute\Bundle\SiteBundle\Entity\Site;
use Mondofute\Bundle\SiteBundle\Repository\SiteRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Mondofute\Bundle\ClientBundle\Form\ClientClientUserType;

class CommandeType extends AbstractType
{
    private $statutDossier;
    private $litigeDossier;

    public function __construct($statutDossier, $litigeDossier)
    {
        $this->statutDossier = $statutDossier;
        $this->litigeDossier = $litigeDossier;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        global $kernel;

        if ('AppCache' == get_class($kernel)) {
            $kernel = $kernel->getKernel();
        }
        $doctrine = $kernel->getContainer()->get('doctrine');

        $siteRepository = $doctrine->getRepository('MondofuteSiteBundle:Site');
        $data = $builder->getData();

        if ($data && null !== $data->getId()) {
            $site = $data->getSite();
        } else {
            $site = $siteRepository->findOneBy(['crm' => true]);
        }

        $locale = $options['locale'];
        $builder
            ->add('prixVente')
            ->add('site', EntityType::class, [
                'class' => Site::class,
                'required' => true,
                'query_builder' => function (SiteRepository $er) {
                    return $er->getSitesSansCrm();
                }
            ])
            ->add('dateCommande')
            ->add('numCommande')
            ->add('clients', EntityType::class, array(
                'class' => Client::class,
                'multiple' => true,
                'expanded' => false,
                'required' => false,
            ))
            ->add('statutDossier', EntityType::class, array(
                'class' => StatutDossier::class,
                'mapped' => false,
                'choice_label' => 'id',
                'query_builder' => function (StatutDossierRepository $r) use ($locale) {
                    $qb = $r->createQueryBuilder('s');
                    $qb->select('s, traductions', 'gsd')
                        ->join('s.traductions', 'traductions')
                        ->join('traductions.langue', 'langue')
                        ->leftJoin('s.groupeStatutDossier', 'gsd')
                        ->leftJoin('gsd.traductions', 'gsdtrad')
                        ->where('langue.code = :code')
                        ->setParameter('code', $locale);
                    return $qb;
                },
                'data' => $this->statutDossier,
            ))
            ->add('litigeDossier', EntityType::class, array(
                'class' => LitigeDossier::class,
                'mapped' => false,
                'choice_label' => 'traductions[0].libelle',
                'placeholder' => '--- Veuillez choisir un litige ---',
                'required' => false,
                'query_builder' => function (LitigeDossierRepository $rep) use ($locale) {
                    $qb = $rep->createQueryBuilder('ld');
                    $qb->select('ld, traductions')
                        ->join('ld.traductions', 'traductions')
                        ->join('traductions.langue', 'langue')
                        ->where('langue.code = :code')
                        ->setParameter('code', $locale);
                    return $qb;
                },
                'data' => $this->litigeDossier,
            ))
            ->add('commandeLignes', PolyCollectionType::class, array(
            'types' => array(
                CommandeLigneSejourType::class,
                CommandeLignePrestationAnnexeType::class,
                CommandeLigneFraisDossierType::class,
                SejourNuiteType::class,
                SejourPeriodeType::class,
                CommandeLigneRemiseType::class,
                RemiseCodePromoType::class,
                RemiseDecoteType::class,
                RemisePromotionType::class,
            ),
            'types_options' => array(
                CommandeLigneSejourType::class => array(// Here you can optionally define options for the InvoiceLineType
                ),
                CommandeLignePrestationAnnexeType::class => array(// Here you can optionally define options for the InvoiceProductLineType
                ),
                CommandeLigneFraisDossierType::class => array(// Here you can optionally define options for the InvoiceProductLineType
                ),
                SejourNuiteType::class => array(// Here you can optionally define options for the InvoiceProductLineType
                ),
                SejourPeriodeType::class => array(// Here you can optionally define options for the InvoiceProductLineType
                    'addSejourPeriode' => $options['addSejourPeriode']
                ),
                CommandeLigneRemiseType::class => array(// Here you can optionally define options for the InvoiceProductLineType
                ),
                RemiseCodePromoType::class => array(// Here you can optionally define options for the InvoiceProductLineType
                    'site' => $site
                ),
                RemiseDecoteType::class => array(// Here you can optionally define options for the InvoiceProductLineType
                    'site' => $site
                ),
                RemisePromotionType::class => array(// Here you can optionally define options for the InvoiceProductLineType
                    'site' => $site
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
            'data_class' => 'Mondofute\Bundle\CommandeBundle\Entity\Commande',
            'locale' => 'fr_FR',
            'addSejourPeriode' => false
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
