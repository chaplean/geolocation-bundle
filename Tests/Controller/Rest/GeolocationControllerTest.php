<?php

namespace Chaplean\Bundle\GeolocationBundle\Tests\Controller\Rest;

use Chaplean\Bundle\UnitBundle\Test\LogicalTest;
use Geocoder\Geocoder;
use Ivory\GoogleMap\Base\Bound;
use Ivory\GoogleMap\Base\Coordinate;
use Ivory\GoogleMap\Services\Geocoding\Result\GeocoderAddressComponent;
use Ivory\GoogleMap\Services\Geocoding\Result\GeocoderGeometry;
use Ivory\GoogleMap\Services\Geocoding\Result\GeocoderResponse;
use Ivory\GoogleMap\Services\Geocoding\Result\GeocoderResult;
use Symfony\Component\HttpKernel\Client;

/**
 * GeolocationControllerTest.php.
 *
 * @author    Valentin - Chaplean <valentin@chaplean.com>
 * @copyright 2014 - 2015 Chaplean (http://www.chaplean.com)
 * @since     1.0.0
 */
class GeolocationControllerTest extends LogicalTest
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
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
    }

    /**
     * @return void
     */
    public function setUp()
    {
        $this->client = static::createClient();
        $this->ivoryGeocoderMock = \Mockery::mock('Ivory\GoogleMap\Services\Geocoding\Geocoder');
        $this->client->getContainer()->set('ivory_google_map.geocoder', $this->ivoryGeocoderMock);
        $this->client->getContainer()->get('chaplean_geolocation.geolocation');
    }

    /**
     * @return void
     */
    public function testGetLongitudeLatitudeAction()
    {
        $this->ivoryGeocoderMock->shouldReceive('geocode')
            ->andReturn(new GeocoderResponse(array(
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
            ), 'OK'));

        $this->client->request('GET', '/rest/geolocation/' . urlencode('9 rue de condé, 33000, Bordeaux'));

        $response = $this->client->getResponse();
        $response = json_decode($response->getContent(), true);

        $this->assertEquals(array('longitude', 'latitude'), array_keys($response));
        $this->assertEquals(-0.5733138, round($response['longitude'], 7));
        $this->assertEquals(44.8435849, round($response['latitude'], 7));
    }

    /**
     * @return void
     */
    public function testSaveAddressAction()
    {
        $this->ivoryGeocoderMock->shouldReceive('geocode')
                                ->andReturn(new GeocoderResponse(array(
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
                                ), 'OK'));

        $this->client->request('POST', '/rest/geolocation', array('address' => '9 rue de Condé, 33000, Bordeaux'));

        $response = $this->client->getResponse();
        $response = json_decode($response->getContent(), true);

        $this->assertEquals(1, $response['id']);
        $this->assertEquals('Rue de Condé', $response['block1']);
        $this->assertEquals(null, $response['block2']);
        $this->assertEquals(null, $response['block3']);
        $this->assertEquals('9', $response['floor']);
        $this->assertEquals('Bordeaux', $response['city']);
        $this->assertEquals('33000', $response['zipcode']);
    }
}
