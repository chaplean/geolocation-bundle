<?php

namespace Chaplean\Bundle\GeolocationBundle\Tests;

use Chaplean\Bundle\GeolocationBundle\Entity\Address;
use Chaplean\Bundle\UnitBundle\Test\LogicalTest;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * EmbeddableAddressTest.php.
 *
 * @author    Valentin - Chaplean <valentin@chaplean.com>
 * @copyright 2014 - 2015 Chaplean (http://www.chaplean.com)
 * @since     3.0.0
 */
class EmbeddableAddressTest extends LogicalTest
{
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
        $newEmbeddableAddress = $this->em->getRepository('Chaplean\Bundle\GeolocationBundle\Tests\EmbeddableAddress')->findAll();

        $this->assertCount(1, $newEmbeddableAddress);
        $this->assertEquals('toto', $newEmbeddableAddress[0]->getAddress()->getBlock1());
        $this->assertEquals('Bourges', $newEmbeddableAddress[0]->getAddress()->getCity());
    }
}

/**
 * Class EmbeddableAddress.
 *
 * @ORM\Entity
 * @ORM\Table(name="cl_embeddable_address")
 */
class EmbeddableAddress
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", options={"unsigned":true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
    /**
     * @ORM\Embedded(class="Chaplean\Bundle\GeolocationBundle\Entity\Address")
     */
    protected $address;

    /**
     * Get address.
     *
     * @return mixed
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set address.
     *
     * @param mixed $address
     *
     * @return self
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }
}
