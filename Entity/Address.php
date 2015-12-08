<?php

namespace Chaplean\Bundle\GeolocationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * Address
 *
 * @ORM\Entity
 * @ORM\Table(name="cl_address")
 */
class Address
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
     * @ORM\Column(type="string", length=200, nullable=false, name="block1")
     *
     * @JMS\Groups({"address_block1", "address_all"})
     */
    private $block1;

    /**
     * @ORM\Column(type="string", length=200, nullable=true, name="block2")
     *
     * @JMS\Groups({"address_block2", "address_all"})
     */
    private $block2;

    /**
     * @ORM\Column(type="string", length=200, nullable=true, name="block3")
     *
     * @JMS\Groups({"address_block3", "address_all"})
     */
    private $block3;

    /**
     * @ORM\Column(type="smallint", nullable=true, name="floor", options={"unsigned":true})
     *
     * @JMS\Groups({"address_floor", "address_all"})
     */
    private $floor;

    /**
     * @ORM\Column(type="string", length=60, nullable=false, name="city_complement")
     *
     * @JMS\Groups({"address_city", "address_all"})
     */
    private $city;

    /**
     * @ORM\Column(type="integer", length=5, nullable=true, name="zipcode")
     *
     * @JMS\Groups({"address_zipcode", "address_all"})
     */
    private $zipcode;

    /**
     * @ORM\Column(type="decimal", length=10, nullable=true, name="longitude", precision=10, scale=7)
     *
     * @JMS\Groups({"address_longitude", "address_all"})
     */
    private $longitude;

    /**
     * @ORM\Column(type="decimal", length=10, nullable=true, name="latitude", scale=7, precision=10)
     *
     * @JMS\Groups({"address_latitude", "address_all"})
     */
    private $latitude;

    /**
     * @ORM\Column(type="datetime", nullable=false, name="date_add")
     *
     * @JMS\Groups({"address_date_add", "address_all"})
     */
    private $dateAdd;

    /**
     * @ORM\Column(type="datetime", nullable=true, name="date_update")
     *
     * @JMS\Groups({"address_date_update", "address_all"})
     */
    private $dateUpdate;

    /**
     * Get id.
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set id.
     *
     * @param mixed $id
     *
     * @return Address
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get block1.
     *
     * @return mixed
     */
    public function getBlock1()
    {
        return $this->block1;
    }

    /**
     * Set block1.
     *
     * @param mixed $block1
     *
     * @return Address
     */
    public function setBlock1($block1)
    {
        $this->block1 = $block1;

        return $this;
    }

    /**
     * Get block2.
     *
     * @return mixed
     */
    public function getBlock2()
    {
        return $this->block2;
    }

    /**
     * Set block2.
     *
     * @param mixed $block2
     *
     * @return Address
     */
    public function setBlock2($block2)
    {
        $this->block2 = $block2;

        return $this;
    }

    /**
     * Get block3.
     *
     * @return mixed
     */
    public function getBlock3()
    {
        return $this->block3;
    }

    /**
     * Set block3.
     *
     * @param mixed $block3
     *
     * @return Address
     */
    public function setBlock3($block3)
    {
        $this->block3 = $block3;

        return $this;
    }

    /**
     * Get floor.
     *
     * @return mixed
     */
    public function getFloor()
    {
        return $this->floor;
    }

    /**
     * Set floor.
     *
     * @param mixed $floor
     *
     * @return Address
     */
    public function setFloor($floor)
    {
        $this->floor = $floor;

        return $this;
    }

    /**
     * Get city.
     *
     * @return mixed
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set city.
     *
     * @param mixed $city
     *
     * @return Address
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get zipcode.
     *
     * @return mixed
     */
    public function getZipcode()
    {
        return $this->zipcode;
    }

    /**
     * Set zipcode.
     *
     * @param mixed $zipcode
     *
     * @return Address
     */
    public function setZipcode($zipcode)
    {
        $this->zipcode = $zipcode;

        return $this;
    }

    /**
     * Get longitude.
     *
     * @return mixed
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * Set longitude.
     *
     * @param mixed $longitude
     *
     * @return Address
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;

        return $this;
    }

    /**
     * Get latitude.
     *
     * @return mixed
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * Set latitude.
     *
     * @param mixed $latitude
     *
     * @return Address
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;

        return $this;
    }

    /**
     * Get dateAdd.
     *
     * @return mixed
     */
    public function getDateAdd()
    {
        return $this->dateAdd;
    }

    /**
     * Set dateAdd.
     *
     * @param mixed $dateAdd
     *
     * @return Address
     */
    public function setDateAdd($dateAdd)
    {
        $this->dateAdd = $dateAdd;

        return $this;
    }

    /**
     * Get dateUpdate.
     *
     * @return mixed
     */
    public function getDateUpdate()
    {
        return $this->dateUpdate;
    }

    /**
     * Set dateUpdate.
     *
     * @param mixed $dateUpdate
     *
     * @return Address
     */
    public function setDateUpdate($dateUpdate)
    {
        $this->dateUpdate = $dateUpdate;

        return $this;
    }

    /**
     * Get concatenated address parts
     *
     * @JMS\VirtualProperty
     * @JMS\SerializedName("address")
     * @JMS\Groups({"address_address", "address_all"})
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->block1 . ' ' .
            ($this->block2 ? $this->block2 . ' ' : '') .
            ($this->block3 ? $this->block3 . ' ' : '') .
            ($this->floor ? $this->floor . ' ' : '') .
            ($this->zipcode ? $this->zipcode . ' ' : '') .
            $this->city;
    }
}
