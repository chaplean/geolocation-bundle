<?php

namespace Tests\Chaplean\Bundle\GeolocationBundle\Resources\Entity;

use Doctrine\ORM\Mapping as ORM;

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
