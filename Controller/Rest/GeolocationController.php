<?php

namespace Chaplean\Bundle\GeolocationBundle\Controller\Rest;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * GeolocationController.php.
 *
 * @author    Valentin - Chaplean <valentin@chaplean.com>
 * @copyright 2014 - 2015 Chaplean (http://www.chaplean.com)
 * @since     1.0.0
 *
 * @Annotations\RouteResource("Geolocation")
 */
class GeolocationController extends FOSRestController
{
    /**
     * @Annotations\Get("/geolocation")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function getRegionDepartmentAction(Request $request)
    {
        $geolocation = $this->get('chaplean_geolocation.ip_location');

        $city = $geolocation->getCityFromIp($request->getClientIp());
        if (!empty($city) && count($city->subdivisions) == 2) {
            $region = $city->subdivisions[0]->names['fr'];
            $department = $city->subdivisions[1]->names['fr'];
            $geo = array(
                'region'     => $region,
                'department' => $department
            );
        } else {
            $geo = array(
                'region'     => null,
                'department' => null
            );
        }

        return $this->handleView($this->view($geo));
    }

    /**
     * Geolocate an address
     *
     * @param string $address The address to geolocate
     *
     * @Annotations\Get("/geolocation/{address}")
     *
     * @return Response
     */
    public function getLongitudeLatitudeAction($address)
    {
        $geocoder = $this->get('chaplean_geolocation.geolocation');

        try {
            $location = $geocoder->getLongitudeLatitudeByAddress($address);
        } catch (\Exception $e) {
            return $this->handleView($this->view('Address not found', 404));
        }

        return $this->handleView($this->view($location));
    }

    /**
     * Save an address
     *
     * @param Request $request
     *
     * @Annotations\Post("/geolocation")
     *
     * @return Response
     */
    public function postAddressAction(Request $request)
    {
        $address = $request->request->get('address', null);

        $geocoder = $this->get('chaplean_geolocation.geolocation');

        try {
            $address = $geocoder->getAddress($address);
        } catch (\Exception $e) {
            return $this->handleView($this->view('Address not found', 400));
        }

        if (!empty($address)) {
            $em = $this->getDoctrine()->getManager();

            $em->persist($address);
            $em->flush();
        }

        return $this->handleView($this->view($address));
    }
}
