<?php

namespace Chaplean\Bundle\GeolocationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Address
 *
 * @ORM\Embeddable
 */
class Address
{
    /**
     * @var string
     *
     * @ORM\Column(type="string", length=200, nullable=false, name="block1")
     *
     * @JMS\Type("string")
     * @JMS\Groups({"address_block1", "address_all"})
     * @Groups({"address_block1", "address_all"})
     *
     * @Assert\NotBlank(
     *     message="general.not_blank",
     *     groups={"Default"}
     * )
     */
    protected $block1;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=200, nullable=true, name="block2")
     *
     * @JMS\Type("string")
     * @JMS\Groups({"address_block2", "address_all"})
     * @Groups({"address_block2", "address_all"})
     */
    protected $block2;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=200, nullable=true, name="block3")
     *
     * @JMS\Type("string")
     * @JMS\Groups({"address_block3", "address_all"})
     * @Groups({"address_block3", "address_all"})
     */
    protected $block3;

    /**
     * @var integer
     *
     * @ORM\Column(type="smallint", nullable=true, name="floor", options={"unsigned":true})
     *
     * @JMS\Groups({"address_floor", "address_all"})
     * @Groups({"address_floor", "address_all"})
     */
    protected $floor;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=60, nullable=false, name="city_complement")
     *
     * @JMS\Type("string")
     * @JMS\Groups({"address_city", "address_all"})
     * @Groups({"address_city", "address_all"})
     *
     * @Assert\NotBlank(
     *     message="general.not_blank",
     *     groups={"Default"}
     * )
     */
    protected $city;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer", length=5, nullable=true, name="zipcode")
     *
     * @JMS\Type("string")
     * @JMS\Groups({"address_zipcode", "address_all"})
     * @JMS\Accessor(getter="getZipcode",setter="setZipcode")
     *
     * @Assert\NotBlank(
     *     message="general.not_blank",
     *     groups={"Default"}
     * )
     */
    protected $zipcode;

    /**
     * @var float
     *
     * @ORM\Column(type="decimal", length=10, nullable=true, name="longitude", precision=10, scale=7)
     *
     * @JMS\Type("float")
     * @JMS\Groups({"address_longitude", "address_all"})
     * @Groups({"address_longitude", "address_all"})
     */
    protected $longitude;

    /**
     * @var float
     *
     * @ORM\Column(type="decimal", length=10, nullable=true, name="latitude", scale=7, precision=10)
     *
     * @JMS\Type("float")
     * @JMS\Groups({"address_latitude", "address_all"})
     * @Groups({"address_latitude", "address_all"})
     */
    protected $latitude;

    /**
     * Get block1.
     *
     * @return string
     */
    public function getBlock1()
    {
        return $this->block1;
    }

    /**
     * Set block1.
     *
     * @param string $block1
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
     * @return string
     */
    public function getBlock2()
    {
        return $this->block2;
    }

    /**
     * Set block2.
     *
     * @param string $block2
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
     * @return string
     */
    public function getBlock3()
    {
        return $this->block3;
    }

    /**
     * Set block3.
     *
     * @param string $block3
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
     * @return integer
     */
    public function getFloor()
    {
        return $this->floor;
    }

    /**
     * Set floor.
     *
     * @param integer $floor
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
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set city.
     *
     * @param string $city
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
     * @Groups({"address_zipcode", "address_all"})
     *
     * @return string
     */
    public function getZipcode(): ?string
    {
        return $this->zipcode !== null ? sprintf('%05d', $this->zipcode) : null;
    }

    /**
     * Set zipcode.
     *
     * @param string $zipcode
     *
     * @return Address
     */
    public function setZipcode($zipcode)
    {
        $this->zipcode = $zipcode !== null
            ? (int) $zipcode
            : null;

        return $this;
    }

    /**
     * Get longitude.
     *
     * @return float
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * Set longitude.
     *
     * @param float $longitude
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
     * @return float
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * Set latitude.
     *
     * @param float $latitude
     *
     * @return Address
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;

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
        return trim(
            $this->block1 .
            ($this->block2 ? ' ' . $this->block2 : '') .
            ($this->block3 ? ' ' . $this->block3 : '') .
            ($this->floor ? ' ' . $this->floor : '') .
            ',' .
            ($this->zipcode ?  ' ' . $this->getZipcode() : '') .
            ' ' . $this->city,
            ', '
        );
    }

    /**
     * Constructs an empty Address
     *
     * @return Address
     */
    public static function constructEmpty()
    {
        $address = new Address();
        $address->setBlock1('');
        $address->setCity('');

        return $address;
    }

    /**
     * Compares this address with another for equality
     *
     * @param Address $other
     *
     * @return boolean
     */
    public function isEqual(Address $other)
    {
        return $this->block1 == $other->block1 &&
               $this->block2 == $other->block2 &&
               $this->block3 == $other->block3 &&
               $this->city == $other->city &&
               $this->floor == $other->floor &&
               $this->zipcode == $other->zipcode &&
               $this->latitude == $other->latitude &&
               $this->longitude == $other->longitude;
    }

    /**
     * @param \Geocoder\Model\Address $addressModel
     *
     * @return self
     */
    public static function fromGeocoderAddressModel(\Geocoder\Model\Address $addressModel) : Address
    {
        $address = new static();

        $address->setBlock1(sprintf('%s %s', $addressModel->getStreetNumber(), $addressModel->getStreetName()));
        $address->setCity($addressModel->getLocality());
        $address->setZipcode($addressModel->getPostalCode());
        $address->setLongitude($addressModel->getCoordinates()->getLongitude());
        $address->setLatitude($addressModel->getCoordinates()->getLatitude());

        return $address;
    }
}
