<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

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
            new SC\DatetimepickerBundle\SCDatetimepickerBundle(),
            new Mondofute\Bundle\StationBundle\MondofuteStationBundle(),
            new Mondofute\Bundle\DomaineBundle\MondofuteDomaineBundle(),
            new Mondofute\Bundle\FournisseurBundle\MondofuteFournisseurBundle(),
            new Mondofute\Bundle\UniteBundle\MondofuteUniteBundle(),
            new Mondofute\Bundle\DescriptionForfaitSkiBundle\MondofuteDescriptionForfaitSkiBundle(),
            new Mondofute\Bundle\ChoixBundle\MondofuteChoixBundle(),
            new Mondofute\Bundle\CatalogueBundle\MondofuteCatalogueBundle(),
            new Mondofute\Bundle\HebergementBundle\MondofuteHebergementBundle(),
            new Nucleus\MoyenComBundle\NucleusMoyenComBundle(),
            new Nucleus\ContactBundle\NucleusContactBundle(),
            new Infinite\FormBundle\InfiniteFormBundle(),
            new \JMS\SerializerBundle\JMSSerializerBundle(),
            new Mondofute\Bundle\RemiseClefBundle\MondofuteRemiseClefBundle(),
            new Mondofute\Bundle\TrancheHoraireBundle\MondofuteTrancheHoraireBundle(),
            new Mondofute\Bundle\LogementBundle\MondofuteLogementBundle(),
            new Mondofute\Bundle\UtilisateurBundle\MondofuteUtilisateurBundle(),
            new Infinite\FormBundle\InfiniteFormBundle(),
            new Nucleus\MoyenComBundle\NucleusMoyenComBundle(),
            new Nucleus\ContactBundle\NucleusContactBundle(),
            new PUGX\MultiUserBundle\PUGXMultiUserBundle(),
            new FOS\UserBundle\FOSUserBundle(),
            new Mondofute\Bundle\CoreBundle\MondofuteCoreBundle(),
            new \Mondofute\Bundle\ClientBundle\MondofuteClientBundle(),
            new Sonata\IntlBundle\SonataIntlBundle(),
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
