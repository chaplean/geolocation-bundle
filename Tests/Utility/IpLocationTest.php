<?php

namespace Tests\Chaplean\Bundle\GeolocationBundle\Utility;

use Chaplean\Bundle\GeolocationBundle\Utility\IpLocation;
use Chaplean\Bundle\UnitBundle\Test\FunctionalTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * IpLocationTest.php.
 *
 * @author    Matthias - Chaplean <matthias@chaplean.coop>
 * @copyright 2014 - 2015 Chaplean (http://www.chaplean.coop)
 * @since     v1.0.0
 */
class IpLocationTest extends FunctionalTestCase
{
    /**
     * @var IpLocation $location
     */
    private $location;

    /**
     * @var RequestStack|\Mockery\MockInterface
     */
    private $requestStack;

    /**
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->requestStack = \Mockery::mock('Symfony\Component\HttpFoundation\RequestStack');
        $this->getContainer()->set('request_stack', $this->requestStack);
        $this->location = $this->getContainer()->get('chaplean_geolocation.ip_location');
    }

    /**
     * @covers \Chaplean\Bundle\GeolocationBundle\Utility\IpLocation::__construct
     * @covers \Chaplean\Bundle\GeolocationBundle\Utility\IpLocation::getCityFromIp()
     *
     * @return void
     */
    public function testGetExistingCityFromIpv4()
    {
        $result = $this->location->getCityFromIp('82.226.243.129');
        $this->assertNotNull($result);
        $this->assertEquals('Bordeaux', $result->city->name);
    }

    /**
     * @covers \Chaplean\Bundle\GeolocationBundle\Utility\IpLocation::__construct
     * @covers \Chaplean\Bundle\GeolocationBundle\Utility\IpLocation::getCityFromIp()
     *
     * @return void
     */
    public function testGetNonExistingCityFromIpv4()
    {
        $result = $this->location->getCityFromIp('0.0.0.0');
        $this->assertNull($result);
    }

    /**
     * @covers \Chaplean\Bundle\GeolocationBundle\Utility\IpLocation::__construct
     * @covers \Chaplean\Bundle\GeolocationBundle\Utility\IpLocation::getCityFromIp()
     *
     * @return void
     */
    public function testGetInvalidIpv4()
    {
        $result = $this->location->getCityFromIp('invalid ip');
        $this->assertNull($result);
    }

    /**
     * @covers \Chaplean\Bundle\GeolocationBundle\Utility\IpLocation::__construct
     * @covers \Chaplean\Bundle\GeolocationBundle\Utility\IpLocation::getCityFromIp()
     *
     * @return void
     */
    public function testGetExistingCityFromIpv6()
    {
        $result = $this->location->getCityFromIp('2001:41d0:a:1308::1');
        $this->assertNotNull($result);
        $this->assertEquals('Paris', $result->city->name);
    }

    /**
     * @covers \Chaplean\Bundle\GeolocationBundle\Utility\IpLocation::__construct
     * @covers \Chaplean\Bundle\GeolocationBundle\Utility\IpLocation::getCityFromIp()
     *
     * @return void
     */
    public function testGetNonExistingCityFromIpv6()
    {
        $result = $this->location->getCityFromIp('::');
        $this->assertNull($result);
    }

    /**
     * @covers \Chaplean\Bundle\GeolocationBundle\Utility\IpLocation::__construct
     * @covers \Chaplean\Bundle\GeolocationBundle\Utility\IpLocation::getCityFromIp()
     *
     * @return void
     */
    public function testGetInvalidIpv6()
    {
        $result = $this->location->getCityFromIp('invalid ip');
        $this->assertNull($result);
    }

    /**
     * @covers \Chaplean\Bundle\GeolocationBundle\Utility\IpLocation::__construct
     * @covers \Chaplean\Bundle\GeolocationBundle\Utility\IpLocation::getCityFromUserIp()
     *
     * @return void
     */
    public function testGetExistingCityFromUser()
    {
        $existingCityRequest = Request::create('', 'GET', [], [], [], ['REMOTE_ADDR' => '82.226.243.129']);
        $this->assertEquals('82.226.243.129', $existingCityRequest->getClientIp());
        $this->requestStack->shouldReceive('getCurrentRequest')
            ->andReturn($existingCityRequest);

        $result = $this->location->getCityFromUserIp();
        $this->assertNotNull($result);
        $this->assertEquals('Bordeaux', $result->city->name);
    }

    /**
     * @covers \Chaplean\Bundle\GeolocationBundle\Utility\IpLocation::__construct
     * @covers \Chaplean\Bundle\GeolocationBundle\Utility\IpLocation::getCityFromUserIp()
     *
     * @return void
     */
    public function testGetNonExistingCityFromUser()
    {
        $nonExistingCityRequest = Request::create('', 'GET', [], [], [], ['REMOTE_ADDR' => '0.0.0.0']);
        $this->assertEquals('0.0.0.0', $nonExistingCityRequest->getClientIp());
        $this->requestStack->shouldReceive('getCurrentRequest')
            ->andReturn($nonExistingCityRequest);

        $result = $this->location->getCityFromUserIp();
        $this->assertNull($result);
    }
}
