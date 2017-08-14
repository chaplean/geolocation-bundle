<?php

namespace Tests\Chaplean\Bundle\GeolocationBundle\Resources\Entity;

use Chaplean\Bundle\GeolocationBundle\Entity\Address;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * Class DummyAddress.
 *
 * @ORM\Entity
 * @ORM\Table(name="cl_dummy_address")
 */
class DummyAddress extends Address
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", options={"unsigned":true})
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @JMS\Groups({"address_id", "address_all"})
     */
    private $id;

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
}
