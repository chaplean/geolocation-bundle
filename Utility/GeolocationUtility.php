<?php

namespace Chaplean\Bundle\GeolocationBundle\Utility;

use Chaplean\Bundle\GeolocationBundle\Entity\Address;
use Geocoder\Geocoder;
use Geocoder\Model\AddressCollection;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

/**
 * GeolocationUtility.php.
 *
 * @author    Valentin - Chaplean <valentin@chaplean.coop>
 * @copyright 2014 - 2015 Chaplean (http://www.chaplean.coop)
 * @since     1.18.0
 */
class GeolocationUtility
{
    /**
     * @var \Geocoder\Provider\GoogleMaps\Model\
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
     * @param Geocoder        $geocoder
     * @param LoggerInterface $logger
     * @param array           $parameters
     */
    public function __construct(Geocoder $geocoder, LoggerInterface $logger, array $parameters)
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

        return [
            'longitude' => $result->getCoordinates()->getLongitude(),
            'latitude'  => $result->getCoordinates()->getLatitude(),
        ];
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
            $this->logger->warning(sprintf('[ChapleanGeolocationBundle] (1) Not found with \'%s\'', $search));
        }

        $search = sprintf('%s %s %s', $address->getBlock2(), $address->getZipcode(), $city);
        try {
            return $this->getLongitudeLatitudeByAddress($search);
        } catch (\Exception $e) {
            $this->logger->warning(sprintf('[ChapleanGeolocationBundle] (2) Not found with \'%s\'', $search));
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
        $class = $this->parameters['persist_entity']['address'];

        if (empty($class)) {
            throw new \Exception('Define \'persist_entity\' configuration, if you want use Address like a class !');
        }

        $result = $this->geocode($address);

        return $class::fromGeocoderAddressModel($result);
    }

    /**
     * @param string $address
     *
     * @return \Geocoder\Model\Address
     * @throws \Exception
     */
    public function geocode($address)
    {
        try {
            /** @var AddressCollection $results */
            $results = $this->geocoder->geocode($address);
        } catch (\Exception $e) {
            $this->logger->error(sprintf('[GeolocationUtility] %s', $e->getMessage()));
            throw $e;
        }

        if ($results->count() > 1) {
            $this->logger->error(sprintf('[GeolocationUtility] More one result ! (%s)', $address));
            throw new \Exception();
        }

        return $results->first();
    }
}
