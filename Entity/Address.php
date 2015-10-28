<?php
namespace Chaplean\Bundle\GeolocationBundle\Entity;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="cl_address")
 */
class Address
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", options={"unsigned":true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=200, nullable=false, name="block1")
     */
    private $block1;

    /**
     * @ORM\Column(type="string", length=200, nullable=true, name="block2")
     */
    private $block2;

    /**
     * @ORM\Column(type="string", length=200, nullable=true, name="block3")
     */
    private $block3;

    /**
     * @ORM\Column(type="smallint", nullable=true, name="floor", options={"unsigned":true})
     */
    private $floor;

    /**
     * @ORM\Column(type="string", length=60, nullable=false, name="city_complement")
     */
    private $city;

    /**
     * @ORM\Column(type="integer", length=5, nullable=true, name="zipcode")
     */
    private $zipcode;

    /**
     * @ORM\Column(type="decimal", length=10, nullable=true, name="longitude", precision=10, scale=7)
     */
    private $longitude;

    /**
     * @ORM\Column(type="decimal", length=10, nullable=true, name="latitude", scale=7, precision=10)
     */
    private $latitude;

    /**
     * @ORM\Column(type="datetime", nullable=false, name="date_add")
     */
    private $dateAdd;

    /**
     * @ORM\Column(type="datetime", nullable=true, name="date_update")
     */
    private $dateUpdate;
}