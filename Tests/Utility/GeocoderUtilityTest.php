<?php

namespace Chaplean\Bundle\GeolocationBundle\Tests\Utility;

use Chaplean\Bundle\GeolocationBundle\Entity\Address;
use Chaplean\Bundle\GeolocationBundle\Utility\GeoLocationUtility;
use Chaplean\Bundle\UnitBundle\Test\LogicalTest;
use Ivory\GoogleMap\Base\Bound;
use Ivory\GoogleMap\Base\Coordinate;
use Ivory\GoogleMap\Services\Geocoding\Geocoder;
use Ivory\GoogleMap\Services\Geocoding\Result\GeocoderAddressComponent;
use Ivory\GoogleMap\Services\Geocoding\Result\GeocoderGeometry;
use Ivory\GoogleMap\Services\Geocoding\Result\GeocoderResponse;
use Ivory\GoogleMap\Services\Geocoding\Result\GeocoderResult;

/**
 * GeocoderUtilityTest.php.
 *
 * @author    Valentin - Chaplean <valentin@chaplean.com>
 * @copyright 2014 - 2015 Chaplean (http://www.chaplean.com)
 * @since     1.0.0
 */
class GeoLocationUtilityTest extends LogicalTest
{
    /**
     * @var GeoLocationUtility
     */
    private $geocoder;

    /**
     * @var Geocoder
     */
    private $ivoryGeocoderMock;

    /**
     * @return void
     */
    public function setUp()
    {
        $this->ivoryGeocoderMock = \Mockery::mock('Ivory\GoogleMap\Services\Geocoding\Geocoder');
        $this->getContainer()->set('ivory_google_map.geocoder', $this->ivoryGeocoderMock);
        $this->geocoder = $this->getContainer()->get('chaplean_geolocation.geolocation');
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function testGetLongitudeLatitudeByAddressWithGoodAddress()
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

        /** @var GeocoderResponse $response */
        $result = $this->geocoder->getLongitudeLatitudeByAddress('9 rue de condé, 33000, Bordeaux');

        $this->assertEquals(44.8435849, round($result['latitude'], 7));
        $this->assertEquals(-0.5733138, round($result['longitude'], 7));
    }

    /**
     * @return void
     *
     * @expectedException \Exception
     * @expectedExceptionMessage ZERO_RESULTS
     */
    public function testGetLongitudeLatitudeByAddressWithBadAddress()
    {
        $this->ivoryGeocoderMock->shouldReceive('geocode')
            ->andReturn(new GeocoderResponse(array(), 'ZERO_RESULTS'));

        /** @var GeocoderResponse $response */
        $this->geocoder->getLongitudeLatitudeByAddress(', , ');
    }

    /**
     * @return void
     */
    public function testGetAddress()
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

        $address = $this->geocoder->getAddress('9 rue de condé, 33000, Bordeaux');

        $this->assertInstanceOf(Address::class, $address);
        $this->assertEquals('9', $address->getFloor());
        $this->assertEquals('Rue de Condé', $address->getBlock1());
        $this->assertEquals('33000', $address->getZipcode());
        $this->assertEquals('Bordeaux', $address->getCity());
    }
}
