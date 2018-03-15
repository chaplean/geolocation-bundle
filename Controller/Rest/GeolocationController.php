<?php

namespace Chaplean\Bundle\GeolocationBundle\Controller\Rest;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * GeolocationController.php.
 *
 * @author    Valentin - Chaplean <valentin@chaplean.coop>
 * @copyright 2014 - 2015 Chaplean (http://www.chaplean.coop)
 * @since     1.0.0
 *
 * @Annotations\RouteResource("Geolocation")
 */
class GeolocationController extends FOSRestController
{
    /**
     * @Annotations\Get("/geolocation")
     *
     * @return Response
     */
    public function getRegionDepartmentAction()
    {
        $geolocation = $this->get('chaplean_geolocation.ip_location');

        $city = $geolocation->getCityFromUserIp();
        $region = null;
        $department = null;
        if (!empty($city)) {
            if (count($city->subdivisions) === 2) {
                $region = $city->subdivisions[0]->names['fr'];
                $department = $city->subdivisions[1]->names['fr'];
            } else if (count($city->subdivisions) === 1){
                $department = $city->subdivisions[0]->names['fr'];
            }
        }

        $geo = [
            'region'     => $region,
            'department' => $department
        ];

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
            return $this->handleView($this->view('Address not found', Response::HTTP_NOT_FOUND));
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
            $this->get('logger')->error(sprintf('GeolocationController:postAddressAction: %s [%s]', $e->getMessage(), $e->getTraceAsString()));
            return $this->handleView($this->view('Address not found', Response::HTTP_BAD_REQUEST));
        }

        if (!empty($address)) {
            $em = $this->getDoctrine()->getManager();

            try {
                $em->persist($address);
                $em->flush();
            } catch (\Exception $e) {
                $this->get('logger')->error(sprintf('GeolocationController:postAddressAction: %s [%s]', $e->getMessage(), $e->getTraceAsString()));
                return $this->handleView($this->view('Address could not be saved', Response::HTTP_INTERNAL_SERVER_ERROR));
            }
        }

        return $this->handleView($this->view($address));
    }
}
