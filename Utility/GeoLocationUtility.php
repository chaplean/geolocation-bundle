<?php

namespace Chaplean\Bundle\GeolocationBundle\Utility;

use Chaplean\Bundle\GeolocationBundle\Entity\Address;
use Ivory\GoogleMap\Services\Geocoding\Geocoder;
use Ivory\GoogleMap\Services\Geocoding\Result\GeocoderAddressComponent;
use Ivory\GoogleMap\Services\Geocoding\Result\GeocoderResponse;

/**
 * GeocoderUtility.php.
 *
 * @author    Valentin - Chaplean <valentin@chaplean.com>
 * @copyright 2014 - 2015 Chaplean (http://www.chaplean.com)
 * @since     1.18.0
 */

class GeoLocationUtility
{
    /**
     * @var Geocoder
     */
    private $geocoder;

    /**
     * @param Geocoder $geocoder
     */
    public function __construct(Geocoder $geocoder)
    {
        $this->geocoder = $geocoder;
    }

    /**
     * @param string $address
     *
     * @return null|array
     * @throws \Exception
     */
    public function getLongitudeLatitudeByAddress($address)
    {
        $result = $this->geocode($address);

        return array(
            'longitude' => $result->getGeometry()->getLocation()->getLongitude(),
            'latitude'  => $result->getGeometry()->getLocation()->getLatitude(),
        );
    }

    /**
     * @param string $address
     *
     * @return Address
     */
    public function getAddress($address)
    {
        $result = $this->geocode($address);

        $address = new Address();

        $block1 = '';
        foreach ($result->getAddressComponents() as $addressComponent) {
            list($value, $type) = $this->getAddressComponement($addressComponent);

            switch ($type) {
                case 'street_number':
                    $block1 .= $value . ' ';
                    break;
                case 'route':
                    $address->setBlock1($block1 . $value);
                    break;
                case 'locality':
                    $address->setCity($value);
                    break;
                case 'postal_code':
                    $address->setZipcode($value);
                    break;
            }
        }

        $address->setLongitude($result->getGeometry()->getLocation()->getLongitude());
        $address->setLatitude($result->getGeometry()->getLocation()->getLatitude());
        $address->setDateAdd(new \DateTime());

        return $address;
    }

    /**
     * @param string $address
     *
     * @return mixed
     * @throws \Exception
     */
    public function geocode($address)
    {
        /** @var GeocoderResponse $response */
        $response = $this->geocoder->geocode($address);

        $results = $response->getResults();

        if ($response->getStatus() != 'OK') {
            throw new \Exception($response->getStatus());
        } elseif (count($results) > 1) {
            throw new \Exception(count($results));
        }

        return $results[0];
    }

    /**
     * @param GeocoderAddressComponent $geocoderAddressComponement
     *
     * @return array
     */
    private function getAddressComponement($geocoderAddressComponement)
    {
        $types = $geocoderAddressComponement->getTypes();
        $type = array_shift($types);

        return array($geocoderAddressComponement->getLongName(), $type);
    }
}
