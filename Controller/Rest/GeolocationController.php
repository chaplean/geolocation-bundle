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

        $location = $geocoder->getLongitudeLatitudeByAddress($address);

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

        $address = $geocoder->getAddress($address);

        if (!empty($address)) {
            $em = $this->getDoctrine()->getManager();

            $em->persist($address);
            $em->flush();
        }

        return $this->handleView($this->view($address));
    }
}
