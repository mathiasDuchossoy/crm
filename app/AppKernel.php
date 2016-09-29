<?php

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new \Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new Mondofute\Bundle\SiteBundle\MondofuteSiteBundle(),
            new Mondofute\Bundle\AccueilBundle\MondofuteAccueilBundle(),
            new Mondofute\Bundle\LangueBundle\MondofuteLangueBundle(),
            new Mondofute\Bundle\GeographieBundle\MondofuteGeographieBundle(),
            new Mondofute\Bundle\StationBundle\MondofuteStationBundle(),
            new Mondofute\Bundle\DomaineBundle\MondofuteDomaineBundle(),
            new Mondofute\Bundle\FournisseurBundle\MondofuteFournisseurBundle(),
            new Mondofute\Bundle\UniteBundle\MondofuteUniteBundle(),
            new Mondofute\Bundle\DescriptionForfaitSkiBundle\MondofuteDescriptionForfaitSkiBundle(),
            new Mondofute\Bundle\ChoixBundle\MondofuteChoixBundle(),
            new Mondofute\Bundle\UtilisateurBundle\MondofuteUtilisateurBundle(),
            new Infinite\FormBundle\InfiniteFormBundle(),
            new Nucleus\MoyenComBundle\NucleusMoyenComBundle(),
            new Nucleus\ContactBundle\NucleusContactBundle(),
            new PUGX\MultiUserBundle\PUGXMultiUserBundle(),
            new FOS\UserBundle\FOSUserBundle(),
            new Mondofute\Bundle\CoreBundle\MondofuteCoreBundle(),
            new \Mondofute\Bundle\ClientBundle\MondofuteClientBundle(),
            new Mondofute\Bundle\CatalogueBundle\MondofuteCatalogueBundle(),
            new Mondofute\Bundle\HebergementBundle\MondofuteHebergementBundle(),
            new \JMS\SerializerBundle\JMSSerializerBundle(),
            new Mondofute\Bundle\RemiseClefBundle\MondofuteRemiseClefBundle(),
            new Mondofute\Bundle\TrancheHoraireBundle\MondofuteTrancheHoraireBundle(),
            new Mondofute\Bundle\LogementBundle\MondofuteLogementBundle(),
            new Mondofute\Bundle\ServiceBundle\MondofuteServiceBundle(),
            new Mondofute\Bundle\PeriodeBundle\MondofutePeriodeBundle(),
            new \JMS\JobQueueBundle\JMSJobQueueBundle(),
            new nucleus\deployBundle\nucleusdeployBundle(),
            new Mondofute\Bundle\MediaBundle\MondofuteMediaBundle(),
            // SonataMediabundle
            new Sonata\MediaBundle\SonataMediaBundle(),
            new Sonata\EasyExtendsBundle\SonataEasyExtendsBundle(),
            new Sonata\IntlBundle\SonataIntlBundle(),
            new Application\Sonata\MediaBundle\ApplicationSonataMediaBundle(),
            new Mondofute\Bundle\PrestationAnnexeBundle\MondofutePrestationAnnexeBundle(),
            new HiDev\Bundle\CodePromoBundle\HiDevCodePromoBundle(),
            new Mondofute\Bundle\CodePromoBundle\MondofuteCodePromoBundle(),
            new Mondofute\Bundle\LogementPeriodeBundle\MondofuteLogementPeriodeBundle(),
            new nucleus\managerBDDBundle\nucleusmanagerBDDBundle(),
            new Mondofute\Bundle\CodePromoApplicationBundle\MondofuteCodePromoApplicationBundle(),
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'), true)) {
            $bundles[] = new Symfony\Bundle\DebugBundle\DebugBundle();
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
        }

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load($this->getRootDir().'/config/config_'.$this->getEnvironment().'.yml');
    }
}
