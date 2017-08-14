<?php

namespace Tests\Chaplean\Bundle\GeolocationBundle\Controller\Rest;

use Chaplean\Bundle\UnitBundle\Test\LogicalTestCase;
use Geocoder\Geocoder;
use Ivory\GoogleMap\Base\Bound;
use Ivory\GoogleMap\Base\Coordinate;
use Ivory\GoogleMap\Services\Geocoding\Result\GeocoderAddressComponent;
use Ivory\GoogleMap\Services\Geocoding\Result\GeocoderGeometry;
use Ivory\GoogleMap\Services\Geocoding\Result\GeocoderResponse;
use Ivory\GoogleMap\Services\Geocoding\Result\GeocoderResult;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Client;

/**
 * GeolocationControllerTest.php.
 *
 * @author    Valentin - Chaplean <valentin@chaplean.coop>
 * @copyright 2014 - 2015 Chaplean (http://www.chaplean.coop)
 * @since     1.0.0
 */
class GeolocationControllerTest extends LogicalTestCase
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var Geocoder
     */
    private $ivoryGeocoderMock;

    /**
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->client = static::createClient();

        $this->ivoryGeocoderMock = \Mockery::mock('Ivory\GoogleMap\Services\Geocoding\Geocoder');
        $this->client->getContainer()
            ->set('ivory_google_map.geocoder', $this->ivoryGeocoderMock);
    }

    /**
     * @covers \Chaplean\Bundle\GeolocationBundle\Controller\Rest\GeolocationController::getLongitudeLatitudeAction()
     *
     * @return void
     */
    public function testGetLongitudeLatitudeAction()
    {
        $this->ivoryGeocoderMock->shouldReceive('geocode')
            ->andReturn(
                new GeocoderResponse(
                    array(
                        new GeocoderResult(
                            array(),
                            '9 Rue de Condé, 33000 Bordeaux, France',
                            new GeocoderGeometry(
                                new Coordinate(44.8435849, -0.5733138, true),
                                'ROOFTOP',
                                new Bound(
                                    new Coordinate(44.8435849, -0.5733138, true),
                                    new Coordinate(44.8435849, -0.5733138, true),
                                    array()
                                ),
                                null
                            ),
                            array(
                                'street_address'
                            ),
                            null
                        ),
                    ), 'OK'
                )
            );

        $this->client->request('GET', '/rest/geolocation/' . urlencode('9 rue de condé, 33000, Bordeaux'));

        $response = $this->client->getResponse();
        $response = json_decode($response->getContent(), true);

        static::assertEquals(array('longitude', 'latitude'), array_keys($response));
        static::assertEquals(-0.5733138, round($response['longitude'], 7));
        static::assertEquals(44.8435849, round($response['latitude'], 7));
    }

    /**
     * @covers \Chaplean\Bundle\GeolocationBundle\Controller\Rest\GeolocationController::getLongitudeLatitudeAction()
     *
     * @return void
     */
    public function testGetLongitudeLatitudeActionWithNotFound()
    {
        $this->ivoryGeocoderMock->shouldReceive('geocode')
            ->andReturn(new GeocoderResponse(array(), 'ZERO_RESULTS'));

        $this->client->request('GET', '/rest/geolocation/' . urlencode(', ,'));

        $response = $this->client->getResponse();

        static::assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        static::assertEquals('"Address not found"', $response->getContent());
    }

    /**
     * @covers \Chaplean\Bundle\GeolocationBundle\Controller\Rest\GeolocationController::postAddressAction()
     *
     * @return void
     */
    public function testSaveAddressAction()
    {
        $this->ivoryGeocoderMock->shouldReceive('geocode')
            ->andReturn(
                new GeocoderResponse(
                    array(
                        new GeocoderResult(
                            array(
                                new GeocoderAddressComponent('9', '9', array('street_number')),
                                new GeocoderAddressComponent('Rue de Condé', 'Rue de Condé', array('route')),
                                new GeocoderAddressComponent('Bordeaux', 'Bordeaux', array('locality', 'political')),
                                new GeocoderAddressComponent('Gironde', 'Gironde', array('administrative_area_level_2', 'political')),
                                new GeocoderAddressComponent('Aquitaine', 'Aquitaine', array('administrative_area_level_1', 'political')),
                                new GeocoderAddressComponent('France', 'FR', array('country', 'political')),
                                new GeocoderAddressComponent('33000', '33000', array('postal_code')),
                            ),
                            '9 Rue de Condé, 33000 Bordeaux, France',
                            new GeocoderGeometry(
                                new Coordinate(44.8435849, -0.5733138, true),
                                'ROOFTOP',
                                new Bound(
                                    new Coordinate(44.8435849, -0.5733138, true),
                                    new Coordinate(44.8435849, -0.5733138, true),
                                    array()
                                ),
                                null
                            ),
                            array(
                                'street_address'
                            ),
                            null
                        ),
                    ), 'OK'
                )
            );

        $this->client->request('POST', '/rest/geolocation', array('address' => '9 rue de Condé, 33000, Bordeaux'));

        $response = $this->client->getResponse();
        $content = json_decode($response->getContent(), true);

        static::assertEquals(Response::HTTP_OK, $response->getStatusCode());
        static::assertEquals(1, $content['id']);
        static::assertEquals('9 Rue de Condé', $content['block1']);
        static::assertEquals(null, $content['block2']);
        static::assertEquals(null, $content['block3']);
        static::assertEquals('Bordeaux', $content['city']);
        static::assertEquals('33000', $content['zipcode']);
    }

    /**
     * @covers \Chaplean\Bundle\GeolocationBundle\Controller\Rest\GeolocationController::postAddressAction()
     *
     * @return void
     */
    public function testSaveAddressNotFoundAction()
    {
        $this->ivoryGeocoderMock->shouldReceive('geocode')
            ->andReturn(new GeocoderResponse(array(), 'ZERO_RESULTS'));

        $this->client->request('POST', '/rest/geolocation', array('address' => ', ,'));

        $response = $this->client->getResponse();

        static::assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        static::assertEquals('"Address not found"', $response->getContent());
    }

    /**
     * @covers \Chaplean\Bundle\GeolocationBundle\Controller\Rest\GeolocationController::getRegionDepartmentAction()
     *
     * @return void
     */
    public function testGetRegionDepartmentAction()
    {
        $this->client->request(
            'GET',
            '/rest/geolocation',
            array(),
            array(),
            array(
                'REMOTE_ADDR' => '82.226.243.129'
            )
        );

        $response = $this->client->getResponse();
        $content = json_decode($response->getContent(), true);

        static::assertTrue(array_key_exists('region', $content));
        static::assertTrue(array_key_exists('department', $content));
        static::assertEquals('Aquitaine', $content['region']);
        static::assertEquals('Gironde', $content['department']);
    }

    /**
     * @covers \Chaplean\Bundle\GeolocationBundle\Controller\Rest\GeolocationController::getRegionDepartmentAction()
     *
     * @return void
     */
    public function testGetRegionDepartmentActionWithoutAddressIp()
    {
        $this->client->request('GET', '/rest/geolocation');

        $response = $this->client->getResponse();
        $content = json_decode($response->getContent(), true);

        static::assertTrue(array_key_exists('region', $content));
        static::assertTrue(array_key_exists('department', $content));
        static::assertNull($content['region']);
        static::assertNull($content['department']);
    }
}
