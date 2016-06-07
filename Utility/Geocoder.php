<?php

namespace Chaplean\Bundle\GeolocationBundle\Utility;

use Chaplean\Bundle\GeolocationBundle\Provider\GeocoderProvider;
use Geocoder\Provider\ProviderInterface;
use Ivory\GoogleMap\Services\Geocoding\Geocoder as IvoryGeocoder;

/**
 * Geocoder.php.
 *
 * @author    Valentin - Chaplean <valentin@chaplean.com>
 * @copyright 2014 - 2016 Chaplean (http://www.chaplean.com)
 * @since     3.1.0
 */
class Geocoder extends IvoryGeocoder
{
    /**
     * Geocoder constructor.
     *
     * @param ProviderInterface      $provider
     * @param string                 $apiKey
     */
    public function __construct(ProviderInterface $provider, $apiKey)
    {
        parent::__construct($provider, null, null);
        $this->getProvider()->setKey($apiKey);
    }

    /**
     * @return GeocoderProvider
     */
    protected function getProvider()
    {
        return parent::getProvider();
    }
}
