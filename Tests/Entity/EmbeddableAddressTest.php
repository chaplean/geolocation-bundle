<?php

namespace Tests\Chaplean\Bundle\GeolocationBundle;

use Chaplean\Bundle\GeolocationBundle\Entity\Address;
use Chaplean\Bundle\UnitBundle\Test\FunctionalTestCase;
use Tests\Chaplean\Bundle\GeolocationBundle\Resources\Entity\EmbeddableAddress;

/**
 * EmbeddableAddressTest.php.
 *
 * @author    Valentin - Chaplean <valentin@chaplean.coop>
 * @copyright 2014 - 2015 Chaplean (http://www.chaplean.coop)
 * @since     3.0.0
 */
class EmbeddableAddressTest extends FunctionalTestCase
{
    /**
     * @return void
     */
    public function testPersistEmbeddableEntity()
    {
        $embeddableAddress = new EmbeddableAddress();
        $address = new Address();
        $address->setBlock1('toto');
        $address->setCity('Bourges');

        $embeddableAddress->setAddress($address);

        $this->em->persist($embeddableAddress);
        $this->em->flush();

        /** @var EmbeddableAddress[] $newEmbeddableAddress */
        $newEmbeddableAddress = $this->em->getRepository('Tests\Chaplean\Bundle\GeolocationBundle\Resources\Entity\EmbeddableAddress')->findAll();

        $this->assertCount(1, $newEmbeddableAddress);
        $this->assertEquals('toto', $newEmbeddableAddress[0]->getAddress()->getBlock1());
        $this->assertEquals('Bourges', $newEmbeddableAddress[0]->getAddress()->getCity());
    }
}
