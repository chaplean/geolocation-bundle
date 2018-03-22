<?php

namespace Tests\Chaplean\Bundle\GeolocationBundle\Entity;

use Chaplean\Bundle\GeolocationBundle\Entity\Address;
use Chaplean\Bundle\UnitBundle\Test\FunctionalTestCase;
use Geocoder\Model\AdminLevelCollection;
use Geocoder\Model\Coordinates;
use Geocoder\Model\Country;
use Geocoder\Provider\GoogleMaps\Model\GoogleAddress;
use Tests\Chaplean\Bundle\GeolocationBundle\Resources\Entity\EmbeddableAddress;

/**
 * Class AddressTest.
 *
 * @package   Tests\Chaplean\Bundle\GeolocationBundle\Entity
 * @author    Matthias - Chaplean <matthias@chaplean.coop>
 * @copyright 2014 - 2017 Chaplean (http://www.chaplean.coop)
 * @since     4.0.0
 */
class AddressTest extends FunctionalTestCase
{
    /**
     * @return array
     */
    public function integerZipcodeToStringProvider()
    {
        return [
            [0,     '00000'],
            [75000, '75000'],
            [5000,  '05000'],
            [null,  null],
        ];
    }

    /**
     * @covers \Chaplean\Bundle\GeolocationBundle\Entity\Address::setZipcode()
     * @covers \Chaplean\Bundle\GeolocationBundle\Entity\Address::getZipcode()
     *
     * @dataProvider integerZipcodeToStringProvider
     *
     * @param integer $zipcode
     * @param string  $expected
     *
     * @return void
     */
    public function testGetZipcode($zipcode, $expected)
    {
        $address = new Address();
        $address->setZipcode($zipcode);

        $this->assertSame($expected, $address->getZipcode());
    }

    /**
     * @return array
     */
    public function addressToExpectedStringsProvider()
    {
        return [
            ['block1', null,     null,     null, null, 'city', 'block1, city'],
            ['block1', 'block2', null,     null, null, 'city', 'block1 block2, city'],
            ['block1', null,     'block3', null, null, 'city', 'block1 block3, city'],
            ['block1', null,     null,     1,    null, 'city', 'block1 1, city'],
            ['block1', null,     null,     null, 1000, 'city', 'block1, 01000 city'],
        ];
    }

    /**
     * @covers \Chaplean\Bundle\GeolocationBundle\Entity\Address
     *
     * @dataProvider addressToExpectedStringsProvider
     *
     * @param string  $block1
     * @param string  $block2
     * @param string  $block3
     * @param integer $floor
     * @param integer $zipcode
     * @param string  $city
     * @param string  $expected
     *
     * @return void
     */
    public function testGetAddress($block1, $block2, $block3, $floor, $zipcode, $city, $expected)
    {
        $address = new Address();
        $address->setBlock1($block1);
        $address->setBlock2($block2);
        $address->setBlock3($block3);
        $address->setFloor($floor);
        $address->setZipcode($zipcode);
        $address->setCity($city);

        $this->assertEquals($expected, $address->getAddress());
    }

    /**
     * @covers \Chaplean\Bundle\GeolocationBundle\Entity\Address::getAddress()
     * @covers \Chaplean\Bundle\GeolocationBundle\Entity\Address::setBlock1()
     * @covers \Chaplean\Bundle\GeolocationBundle\Entity\Address::setCity()
     * @covers \Chaplean\Bundle\GeolocationBundle\Entity\Address::getAddress()
     * @covers \Chaplean\Bundle\GeolocationBundle\Entity\Address::constructEmpty()
     *
     * @return void
     */
    public function testConstructEmpty()
    {
        $addressContainer = new EmbeddableAddress();
        $address = Address::constructEmpty();
        $addressContainer->setAddress($address);

        $this->em->persist($addressContainer);
        $this->em->flush();

        $this->assertEquals('', $address->getAddress());
    }

    /**
     * @covers \Chaplean\Bundle\GeolocationBundle\Entity\Address::isEqual()
     *
     * @return void
     */
    public function testIsEqual()
    {
        $address1 = new Address();
        $address2 = new Address();

        $this->assertTrue($address1->isEqual($address2));
    }

    /**
     * @covers \Chaplean\Bundle\GeolocationBundle\Entity\Address::isEqual()
     * @covers \Chaplean\Bundle\GeolocationBundle\Entity\Address::setLatitude()
     * @covers \Chaplean\Bundle\GeolocationBundle\Entity\Address::setLongitude()
     *
     * @return void
     */
    public function testIsNotEqual()
    {
        $address1 = new Address();
        $address2 = new Address();
        $address1->setLatitude(1);
        $address1->setLongitude(1);

        $this->assertFalse($address1->isEqual($address2));
    }

    /**
     * @covers \Chaplean\Bundle\GeolocationBundle\Entity\Address::fromGeocoderAddressModel()
     * @covers \Chaplean\Bundle\GeolocationBundle\Entity\Address::setBlock1()
     * @covers \Chaplean\Bundle\GeolocationBundle\Entity\Address::setCity()
     * @covers \Chaplean\Bundle\GeolocationBundle\Entity\Address::setZipcode()
     * @covers \Chaplean\Bundle\GeolocationBundle\Entity\Address::setLongitude()
     * @covers \Chaplean\Bundle\GeolocationBundle\Entity\Address::setLatitude()
     * @covers \Chaplean\Bundle\GeolocationBundle\Entity\Address::getBlock1()
     * @covers \Chaplean\Bundle\GeolocationBundle\Entity\Address::getBlock2()
     * @covers \Chaplean\Bundle\GeolocationBundle\Entity\Address::getCity()
     * @covers \Chaplean\Bundle\GeolocationBundle\Entity\Address::getZipcode()
     * @covers \Chaplean\Bundle\GeolocationBundle\Entity\Address::getLongitude()
     * @covers \Chaplean\Bundle\GeolocationBundle\Entity\Address::getLatitude()
     *
     * @return void
     */
    public function testFromGeocoderAddressModel()
    {
        $address = Address::fromGeocoderAddressModel(new GoogleAddress(
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
        ));

        $this->assertEquals('9 Rue de Condé', $address->getBlock1());
        $this->assertNull($address->getBlock2());
        $this->assertEquals('33000', $address->getZipcode());
        $this->assertEquals('Bordeaux', $address->getCity());
        $this->assertEquals(44.8435229, $address->getLatitude());
        $this->assertEquals(-0.573404, $address->getLongitude());
    }
}
