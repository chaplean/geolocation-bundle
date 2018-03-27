<?php

namespace Tests\Chaplean\Bundle\GeolocationBundle\Utility;

use Chaplean\Bundle\GeolocationBundle\Entity\Address;
use Chaplean\Bundle\GeolocationBundle\Utility\GeolocationUtility;
use Chaplean\Bundle\UnitBundle\Test\FunctionalTestCase;
use Geocoder\Exception\InvalidCredentials;
use Geocoder\Model\AddressCollection;
use Geocoder\Model\AdminLevelCollection;
use Geocoder\Model\Coordinates;
use Geocoder\Model\Country;
use Geocoder\Model\Address as GeocoderAddress;
use Geocoder\Plugin\PluginProvider;
use Geocoder\Provider\GoogleMaps\Model\GoogleAddress;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\MockInterface;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

/**
 * GeolocationUtilityTest.php.
 *
 * @author    Valentin - Chaplean <valentin@chaplean.coop>
 * @copyright 2014 - 2015 Chaplean (http://www.chaplean.coop)
 * @since     1.0.0
 */
class GeolocationUtilityTest extends FunctionalTestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var GeolocationUtility
     */
    private $GeolocationUtility;

    /**
     * @var PluginProvider|MockInterface
     */
    private $geocoder;

    /**
     * @var Logger|MockInterface
     */
    private $logger;

    /**
     * @return void
     * @throws
     */
    public function setUp()
    {
        parent::setUp();

        $this->geocoder = \Mockery::mock(PluginProvider::class);
        $this->logger = \Mockery::mock(LoggerInterface::class);
        // Comment this line for test no breaking change in BazingaGeocoder !
        $this->getContainer()->set('bazinga_geocoder.provider.google_maps', $this->geocoder);

        $this->getContainer()->set('logger', $this->logger);

        $this->GeolocationUtility = $this->getContainer()->get('chaplean_geolocation.geolocation');
    }

    /**
     * @covers \Chaplean\Bundle\GeolocationBundle\Utility\GeolocationUtility::__construct
     * @covers \Chaplean\Bundle\GeolocationBundle\Utility\GeolocationUtility::geocode()
     * @covers \Chaplean\Bundle\GeolocationBundle\Utility\GeolocationUtility::getLongitudeLatitudeByAddress()
     *
     * @return void
     * @throws
     */
    public function testGetLongitudeLatitudeByAddressWithGoodAddress()
    {
        $this->geocoder->shouldReceive('geocodeQuery')->once()->andReturn(
                new AddressCollection([
                    new GoogleAddress(
                        '',
                        new AdminLevelCollection(),
                        new Coordinates(44.8435229, -0.573404),
                        null,
                        '9',
                        'Rue de Condé',
                        '33000',
                        'Bordeaux',
                        null,
                        new Country('France', 'FR'),
                        null
                    )
                ])
            );

        $result = $this->GeolocationUtility->getLongitudeLatitudeByAddress('9 rue de condé, 33000, Bordeaux');

        $this->assertEquals(44.8435229, round($result['latitude'], 7));
        $this->assertEquals(-0.573404, round($result['longitude'], 7));
    }

    /**
     * @covers \Chaplean\Bundle\GeolocationBundle\Utility\GeolocationUtility::__construct
     * @covers \Chaplean\Bundle\GeolocationBundle\Utility\GeolocationUtility::geocode()
     * @covers \Chaplean\Bundle\GeolocationBundle\Utility\GeolocationUtility::findLongitudeLatitudeByAddress()
     *
     * @return void
     * @throws
     */
    public function testfindLongitudeLatitudeByAddressWithGoodAddress()
    {
        $this->geocoder->shouldReceive('geocodeQuery')->once()->andReturn(
            new AddressCollection([
                new GoogleAddress(
                    '',
                    new AdminLevelCollection(),
                    new Coordinates(44.8435229, -0.573404),
                    null,
                    '9',
                    'Rue de Condé',
                    '33000',
                    'Bordeaux',
                    null,
                    new Country('France', 'FR'),
                    null
                )
            ])
        );

        $address = new Address();
        $address->setBlock1('9 rue de condé');
        $address->setBlock2('');
        $address->setCity('Bordeaux');
        $address->setZipcode('33000');

        $result = $this->GeolocationUtility->findLongitudeLatitudeByAddress($address);

        $this->assertEquals(44.8435229, round($result['latitude'], 7));
        $this->assertEquals(-0.573404, round($result['longitude'], 7));
    }

    /**
     * @covers \Chaplean\Bundle\GeolocationBundle\Utility\GeolocationUtility::__construct
     * @covers \Chaplean\Bundle\GeolocationBundle\Utility\GeolocationUtility::geocode()
     * @covers \Chaplean\Bundle\GeolocationBundle\Utility\GeolocationUtility::findLongitudeLatitudeByAddress()
     *
     * @return void
     * @throws
     */
    public function testfindLongitudeLatitudeByAddressWithSubAddressNotFound()
    {
        $this->geocoder->shouldReceive('geocodeQuery')->once()->andReturn(new AddressCollection([]));
        $this->logger->shouldReceive('warning')->once();

        $this->geocoder->shouldReceive('geocodeQuery')->once()->andReturn(
            new AddressCollection([
                new GoogleAddress(
                    '',
                    new AdminLevelCollection(),
                    new Coordinates(48.8896563, 2.2422251),
                    null,
                    '9',
                    'Rue de Condé',
                    '33000',
                    'Bordeaux',
                    null,
                    new Country('France', 'FR'),
                    null
                )
            ])
        );

        $address = new Address();
        $address->setBlock1('Tour Atlantique');
        $address->setBlock2('1, place de la pyramide');
        $address->setCity('Paris La Défense');
        $address->setZipcode('92911');

        $result = $this->GeolocationUtility->findLongitudeLatitudeByAddress($address);

        $this->assertEquals(48.8896563, round($result['latitude'], 7));
        $this->assertEquals(2.2422251, round($result['longitude'], 7));
    }

    /**
     * @covers \Chaplean\Bundle\GeolocationBundle\Utility\GeolocationUtility::__construct
     * @covers \Chaplean\Bundle\GeolocationBundle\Utility\GeolocationUtility::geocode()
     * @covers \Chaplean\Bundle\GeolocationBundle\Utility\GeolocationUtility::getLongitudeLatitudeByAddress()
     *
     * @return void
     *
     * @expectedException \Exception
     */
    public function testGetLongitudeLatitudeByAddressWithBadAddress()
    {
        $this->geocoder->shouldReceive('geocodeQuery')->once()->andReturn(new AddressCollection([]));
        $this->logger->shouldReceive('error')->once();

        $this->GeolocationUtility->getLongitudeLatitudeByAddress(', , ');
    }

    /**
     * @covers \Chaplean\Bundle\GeolocationBundle\Utility\GeolocationUtility::__construct
     * @covers \Chaplean\Bundle\GeolocationBundle\Utility\GeolocationUtility::geocode()
     * @covers \Chaplean\Bundle\GeolocationBundle\Utility\GeolocationUtility::findLongitudeLatitudeByAddress()
     *
     * @return void
     *
     * @expectedException \Exception
     */
    public function testGetLongitudeLatitudeByAddressWithNotFoundAddress()
    {
        $this->geocoder->shouldReceive('geocodeQuery')->twice()->andReturn(new AddressCollection([]));
        $this->logger->shouldReceive('error')->twice();
        $this->logger->shouldReceive('warning')->twice();

        $address = new Address();

        $this->GeolocationUtility->findLongitudeLatitudeByAddress($address);
    }

    /**
     * @covers \Chaplean\Bundle\GeolocationBundle\Utility\GeolocationUtility::__construct
     * @covers \Chaplean\Bundle\GeolocationBundle\Utility\GeolocationUtility::geocode()
     * @covers \Chaplean\Bundle\GeolocationBundle\Utility\GeolocationUtility::getAddress()
     *
     * @return void
     * @throws
     */
    public function testGetAddress()
    {
        $this->geocoder->shouldReceive('geocodeQuery')->once()->andReturn(
            new AddressCollection([
                new GoogleAddress(
                    '',
                    new AdminLevelCollection(),
                    new Coordinates(44.8435229, -0.573404),
                    null,
                    '9',
                    'Rue de Condé',
                    '33000',
                    'Bordeaux',
                    null,
                    new Country('France', 'FR'),
                    null
                )
            ])
        );

        $address = $this->GeolocationUtility->getAddress('9 rue de condé, 33000, Bordeaux');

        $this->assertInstanceOf(Address::class, $address);
        $this->assertEquals('9 Rue de Condé', $address->getBlock1());
        $this->assertEquals('33000', $address->getZipcode());
        $this->assertEquals('Bordeaux', $address->getCity());
    }

    /**
     * @covers \Chaplean\Bundle\GeolocationBundle\Utility\GeolocationUtility::cleanCity()
     *
     * @dataProvider citiesProvider
     *
     * @param string $expected
     * @param string $city
     *
     * @return void
     */
    public function testCleanCity($expected, $city)
    {
        $cityCleaned = $this->GeolocationUtility->cleanCity($city);

        $this->assertEquals($expected, $cityCleaned);
    }

    /**
     * @covers \Chaplean\Bundle\GeolocationBundle\Utility\GeolocationUtility::geocode
     *
     * @return void
     * @expectedException \Geocoder\Exception\InvalidArgument
     * @throws
     */
    public function testGeocodeInvalidArgument()
    {
        $this->logger->shouldReceive('error')->once();

        $this->GeolocationUtility->geocode('');
    }

    /**
     * @covers \Chaplean\Bundle\GeolocationBundle\Utility\GeolocationUtility::geocode
     *
     * @return void
     * @expectedException \Geocoder\Exception\InvalidCredentials
     */
    public function testGeocodeInvalidCredentical()
    {
        $this->geocoder->shouldReceive('geocodeQuery')->once()->andThrow(new InvalidCredentials());
        $this->logger->shouldReceive('error')->once();

        $this->GeolocationUtility->geocode(', ');
    }

    /**
     * @covers \Chaplean\Bundle\GeolocationBundle\Utility\GeolocationUtility::geocode
     *
     * @return void
     * @expectedException \Exception
     */
    public function testGeocodeMoreOneResult()
    {
        $this->geocoder->shouldReceive('geocodeQuery')->once()->andReturn(new AddressCollection([
            \Mockery::mock(GeocoderAddress::class),
            \Mockery::mock(GeocoderAddress::class),
        ]));
        $this->logger->shouldReceive('error')->once();

        $this->GeolocationUtility->geocode('vzmldkmlz ');
    }

    /**
     * @return array
     */
    public function citiesProvider()
    {
        return [
            ['', 'Cedex 0'],
            ['', 'CeDeX 0'],
            ['', 'CEDEX 0'],
            ['', 'Cedex5'],
            ['', 'Cedex'],
            ['Bordeaux ', 'Bordeaux CEDEX'],
            ['Bordeaux ', 'Bordeaux CeDeX 201'],
        ];
    }
}
