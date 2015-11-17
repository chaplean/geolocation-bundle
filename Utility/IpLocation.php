<?php

namespace Chaplean\Bundle\GeolocationBundle\Utility;

use Cravler\MaxMindGeoIpBundle\Service\GeoIpService;
use GeoIp2\Exception\AddressNotFoundException;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * IpLocation.php.
 *
 * @author    Matthias - Chaplean <matthias@chaplean.com>
 * @copyright 2014 - 2015 Chaplean (http://www.chaplean.com)
 * @since     v1.0.0
 */
class IpLocation
{
    /**
     * @var RequestStack $request
     */
    private $requestStack;

    /**
     * @var GeoIpService $geoIp
     */
    private $geoIp;

    /**
     * @param RequestStack $requestStack
     * @param GeoIpService $geoIp
     */
    public function __construct(RequestStack $requestStack, GeoIpService $geoIp)
    {
        $this->requestStack = $requestStack;
        $this->geoIp = $geoIp;
    }

    /**
     * Return a City corresponding to the given IP
     *
     * @param string $ip The ip to perform the lookup with
     *
     * @return City|null
     */
    public function getCityFromIp($ip)
    {
        try {
            $result = $this->geoIp->getRecord($ip, 'city');
        } catch (AddressNotFoundException $e) {
            return null;
        } catch (\InvalidArgumentException $e) {
            return null;
        }

        return $result;
    }

    /**
     * Return a City corresponding to the user's IP
     *
     * @return City|null
     */
    public function getCityFromUserIp()
    {
        $request = $this->requestStack->getCurrentRequest();
        $ip = $request->getClientIp();

        return $this->getCityFromIp($ip);
    }
}
