<?php

namespace Tests\Chaplean\Bundle\GeolocationBundle\Controller\Rest;

use Chaplean\Bundle\UnitBundle\Test\FunctionalTestCase;
use Geocoder\Model\AddressCollection;
use Geocoder\Model\AdminLevelCollection;
use Geocoder\Model\Coordinates;
use Geocoder\Model\Country;
use Geocoder\Plugin\PluginProvider;
use Geocoder\Provider\GoogleMaps\Model\GoogleAddress;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\MockInterface;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Client;


/**
 * GeolocationControllerTest.php.
 *
 * @author    Valentin - Chaplean <valentin@chaplean.coop>
 * @copyright 2014 - 2015 Chaplean (http://www.chaplean.coop)
 * @since     1.0.0
 */
class GeolocationControllerTest extends FunctionalTestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var Client
     */
    private $client;

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
     */
    public function setUp()
    {
        parent::setUp();

        $this->client = self::createClient();

        $this->geocoder = \Mockery::mock(PluginProvider::class);
        $this->logger = \Mockery::mock(LoggerInterface::class);
        // Comment this line for test no breaking change in BazingaGeocoder !
        $this->client->getContainer()->set('bazinga_geocoder.provider.google_maps', $this->geocoder);
        $this->client->getContainer()->set('logger', $this->logger);

    }

    /**
     * @covers \Chaplean\Bundle\GeolocationBundle\Controller\Rest\GeolocationController::getLongitudeLatitudeAction()
     *
     * @return void
     */
    public function testGetLongitudeLatitudeAction()
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

        $this->client->request('GET', '/rest/geolocation/' . urlencode('9 rue de condé, 33000, Bordeaux'));

        $response = $this->client->getResponse();
        $response = json_decode($response->getContent(), true);

        static::assertEquals(['longitude', 'latitude'], array_keys($response));
        static::assertEquals(-0.573404, round($response['longitude'], 7));
        static::assertEquals(44.8435229, round($response['latitude'], 7));
    }

    /**
     * @covers \Chaplean\Bundle\GeolocationBundle\Controller\Rest\GeolocationController::getLongitudeLatitudeAction()
     *
     * @return void
     */
    public function testGetLongitudeLatitudeActionWithNotFound()
    {
        $this->geocoder->shouldReceive('geocodeQuery')->once()->andReturn(new AddressCollection([]));

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

        $this->client->request('POST', '/rest/geolocation', ['address' => '9 rue de Condé, 33000, Bordeaux']);

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
        $this->geocoder->shouldReceive('geocodeQuery')->once()->andReturn(new AddressCollection([]));
        $this->logger->shouldReceive('error')->twice();

        $this->client->request('POST', '/rest/geolocation', ['address' => ', ,']);

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
            [],
            [],
            [
                'REMOTE_ADDR' => '82.226.243.129'
            ]
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
