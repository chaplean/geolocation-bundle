<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

/**
 * Class AppKernel.
 *
 * @author    Valentin - Chaplean <valentin@chaplean.coop>
 * @copyright 2014 - 2015 Chaplean (http://www.chaplean.coop)
 * @since     1.0.0
 */
class AppKernel extends Kernel
{
    /**
     * @return array
     */
    public function registerBundles()
    {
        $bundles = [
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Chaplean\Bundle\UnitBundle\ChapleanUnitBundle(),
            new Liip\FunctionalTestBundle\LiipFunctionalTestBundle(),
            new Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new Cravler\MaxMindGeoIpBundle\CravlerMaxMindGeoIpBundle(),
            new Chaplean\Bundle\GeolocationBundle\ChapleanGeolocationBundle(),
            new FOS\RestBundle\FOSRestBundle(),
            new JMS\SerializerBundle\JMSSerializerBundle(),
            new Bazinga\Bundle\GeocoderBundle\BazingaGeocoderBundle(),
        ];

        return $bundles;
    }

    /**
     * @return string
     */
    public function getRootDir()
    {
        return __DIR__;
    }

    /**
     * @return string
     */
    public function getCacheDir()
    {
        return dirname(__DIR__) . '/var/cache/' . $this->getEnvironment();
    }

    /**
     * @return string
     */
    public function getLogDir()
    {
        return dirname(__DIR__) . '/var/logs';
    }

    /**
     * @param LoaderInterface $loader Resource loader.
     *
     * @return void
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__ . '/config/config.yml');
    }
}
