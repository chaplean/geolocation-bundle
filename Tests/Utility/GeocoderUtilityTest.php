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

        $result = $this->geocoder->getLongitudeLatitudeByAddress('9 rue de condé, 33000, Bordeaux');

        static::assertEquals(44.8435849, round($result['latitude'], 7));
        static::assertEquals(-0.5733138, round($result['longitude'], 7));
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function testfindLongitudeLatitudeByAddressWithGoodAddress()
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

        $address = new Address();
        $address->setBlock1('9 rue de condé');
        $address->setBlock2('');
        $address->setCity('Bordeaux');
        $address->setZipcode('33000');

        $result = $this->geocoder->findLongitudeLatitudeByAddress($address);

        static::assertEquals(44.8435849, round($result['latitude'], 7));
        static::assertEquals(-0.5733138, round($result['longitude'], 7));
    }


    /**
     * @return void
     * @throws \Exception
     */
    public function testfindLongitudeLatitudeByAddressWithSubAddressNotFound()
    {
        $this->ivoryGeocoderMock->shouldReceive('geocode')
            ->andReturn(new GeocoderResponse(array(), 'ZERO_RESULTS'))->once();
        $this->ivoryGeocoderMock->shouldReceive('geocode')
            ->andReturn(new GeocoderResponse(array(
                new GeocoderResult(
                    array(),
                    '1 Place de la Pyramide, 92800 Puteaux, France',
                    new GeocoderGeometry(
                        new Coordinate(48.8895717, 2.2420858, true),
                        'ROOFTOP',
                        new Bound(
                            new Coordinate(48.888222669708, 2.2407368197085, true),
                            new Coordinate(48.890920630291, 2.2434347802915, true),
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

        $address = new Address();
        $address->setBlock1('Tour Atlantique');
        $address->setBlock2('1, place de la pyramide ');
        $address->setCity('Paris La Défense');
        $address->setZipcode('92911');

        $result = $this->geocoder->findLongitudeLatitudeByAddress($address);

        static::assertEquals(48.8895717, round($result['latitude'], 7));
        static::assertEquals(2.2420858, round($result['longitude'], 7));
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
     *
     * @expectedException \Exception
     * @expectedExceptionMessage ZERO_RESULTS
     */
    public function testGetLongitudeLatitudeByAddressWithNotFoundAddress()
    {
        $this->ivoryGeocoderMock->shouldReceive('geocode')
            ->andReturn(new GeocoderResponse(array(), 'ZERO_RESULTS'));

        $address = new Address();

        /** @var GeocoderResponse $response */
        $this->geocoder->findLongitudeLatitudeByAddress($address);
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

        static::assertInstanceOf(Address::class, $address);
        static::assertEquals('9 Rue de Condé', $address->getBlock1());
        static::assertEquals('33000', $address->getZipcode());
        static::assertEquals('Bordeaux', $address->getCity());
    }
//    public function testFindLongitudeLatitude()
//    {
////        $address = new Address();
////        $address->setBlock1('Tour Atlantique');
////        $address->setBlock2('1, place de la pyramide ');
////        $address->setCity('Paris La Défense');
////        $address->setZipcode('92911');
////
////        $result = $this->geocoder->findLongitudeLatitudeByAddress($address);
////
////        $address = new Address();
////        $address->setBlock1('17 rue du Bois Briand');
////        $address->setBlock2('CS 83589');
////        $address->setCity('NANTES cedex 3');
////        $address->setZipcode('44335');
////
////        $result = $this->geocoder->findLongitudeLatitudeByAddress($address);
//    }
}
