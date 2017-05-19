<?php

namespace Tests\Chaplean\Bundle\GeolocationBundle\Entity;

use Chaplean\Bundle\GeolocationBundle\Entity\Address;
use Chaplean\Bundle\UnitBundle\Test\LogicalTestCase;
use Tests\Chaplean\Bundle\GeolocationBundle\EmbeddableAddress;

/**
 * Class AddressTest.
 *
 * @package   Tests\Chaplean\Bundle\GeolocationBundle\Entity
 * @author    Matthias - Chaplean <matthias@chaplean.com>
 * @copyright 2014 - 2017 Chaplean (http://www.chaplean.com)
 * @since     4.0.0
 */
class AddressTest extends LogicalTestCase
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
            ['block1', null, null, null, null,      'city', 'block1, city'],
            ['block1', 'block2', null, null, null,  'city', 'block1 block2, city'],
            ['block1', null, 'block3', null, null,  'city', 'block1 block3, city'],
            ['block1', null, null, 1, null, 'city', 'block1 1, city'],
            ['block1', null, null, null, 1000,      'city', 'block1, 01000 city'],
        ];
    }

    /**
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
}
