<?php

namespace Chaplean\Bundle\GeolocationBundle\Utility;

use Chaplean\Bundle\GeolocationBundle\Entity\Address;
use Ivory\GoogleMap\Services\Geocoding\Geocoder;
use Ivory\GoogleMap\Services\Geocoding\Result\GeocoderAddressComponent;
use Ivory\GoogleMap\Services\Geocoding\Result\GeocoderResponse;
use Monolog\Logger;

/**
 * GeocoderUtility.php.
 *
 * @author    Valentin - Chaplean <valentin@chaplean.coop>
 * @copyright 2014 - 2015 Chaplean (http://www.chaplean.coop)
 * @since     1.18.0
 */
class GeoLocationUtility
{
    /**
     * @var Geocoder
     */
    private $geocoder;

    /**
     * @var array
     */
    private $parameters;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @param Geocoder $geocoder
     * @param Logger   $logger
     * @param array    $parameters
     */
    public function __construct(Geocoder $geocoder, Logger $logger, array $parameters)
    {
        $this->geocoder = $geocoder;
        $this->parameters = $parameters;
        $this->logger = $logger;
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
     * @param Address $address
     *
     * @return null|array
     * @throws \Exception
     */
    public function findLongitudeLatitudeByAddress(Address $address)
    {
        $city = $this->cleanCity($address->getCity());

        $search = sprintf('%s %s %s', $address->getBlock1(), $address->getZipcode(), $city);
        try {
            return $this->getLongitudeLatitudeByAddress($search);
        } catch (\Exception $e) {
            $this->logger->warn(sprintf('[ChapleanGeolocationBundle] (1) Not found with \'%s\'', $search));
        }

        $search = sprintf('%s %s %s', $address->getBlock2(), $address->getZipcode(), $city);
        try {
            return $this->getLongitudeLatitudeByAddress($search);
        } catch (\Exception $e) {
            $this->logger->warn(sprintf('[ChapleanGeolocationBundle] (2) Not found with \'%s\'', $search));
            throw $e;
        }
    }

    /**
     * @param string $city
     *
     * @return string
     */
    public function cleanCity($city)
    {
        return preg_replace('/([C|c][E|e][D|d][E|e][X|x]\s*\d*)/', '', $city);
    }

    /**
     * @param string $address
     *
     * @return mixed
     * @throws \Exception
     */
    public function getAddress($address)
    {
        if (empty($this->parameters['persist_entity']['address'])) {
            throw new \Exception('Define \'%s\' configuration, if you want use Address like a class !');
        }

        $result = $this->geocode($address);

        $class = $this->parameters['persist_entity']['address'];
        /** @var mixed $address */
        $address = new $class();

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

        if ($response->getStatus() !== 'OK') {
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
    private function getAddressComponement(GeocoderAddressComponent $geocoderAddressComponement)
    {
        $types = $geocoderAddressComponement->getTypes();
        $type = array_shift($types);

        return array($geocoderAddressComponement->getLongName(), $type);
    }
}
