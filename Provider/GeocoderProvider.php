<?php

namespace Chaplean\Bundle\GeolocationBundle\Provider;

use Geocoder\HttpAdapter\HttpAdapterInterface;
use Ivory\GoogleMap\Services\Geocoding\GeocoderProvider as IvoryGeocoderProvider;
use Ivory\GoogleMap\Services\Geocoding\GeocoderRequest;

/**
 * GeocoderProvider.php.
 *
 * @author    Valentin - Chaplean <valentin@chaplean.com>
 * @copyright 2014 - 2016 Chaplean (http://www.chaplean.com)
 * @since     3.1.0
 */
class GeocoderProvider extends IvoryGeocoderProvider
{
    /**
     * @var string
     */
    protected $key;

    /**
     * GeocoderProvider constructor.
     *
     * @param HttpAdapterInterface $adapter
     * @param string               $key
     * @param string               $locale
     */
    public function __construct(HttpAdapterInterface $adapter, $key, $locale)
    {
        $this->key = $key;
        parent::__construct($adapter, $locale);
    }

    /**
     * @param GeocoderRequest $geocoderRequest
     *
     * @return string
     */
    public function generateUrl(GeocoderRequest $geocoderRequest)
    {
        $this->setHttps(true);
        
        $url = parent::generateUrl($geocoderRequest);

        if ($this->key) {
            $url = sprintf('%s&key=%s', $url, $this->key);
        }

        return $url;
    }
}
