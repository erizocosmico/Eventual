<?php

namespace Eventual\Bundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Event
 *
 * @ORM\Table(name="eventual_event")
 * @ORM\Entity
 */
class Event
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="place_name", type="string", length=255)
     */
    private $placeName;

    /**
     * @var string
     *
     * @ORM\Column(name="coords_lat", type="decimal", precision=14, scale=8, nullable=true)
     */
    private $coordsLat;

    /**
     * @var string
     *
     * @ORM\Column(name="coords_long", type="decimal", precision=14, scale=8, nullable=true)
     */
    private $coordsLong;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     */
    private $description;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="event_date", type="datetime")
     */
    private $date;

    /**
     * @var EventCollection
     *
     * @ORM\ManyToOne(targetEntity="EventCollection", inversedBy="events")
     * @ORM\JoinColumn(name="collection_id", referencedColumnName="id")
     */
    private $collection;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Event
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set placeName
     *
     * @param string $placeName
     * @return Event
     */
    public function setPlaceName($placeName)
    {
        $this->placeName = $placeName;

        return $this;
    }

    /**
     * Get placeName
     *
     * @return string
     */
    public function getPlaceName()
    {
        return $this->placeName;
    }

    /**
     * Set coordsLat
     *
     * @param string $coordsLat
     * @return Event
     */
    public function setCoordsLat($coordsLat)
    {
        $this->coordsLat = $coordsLat;

        return $this;
    }

    /**
     * Get coordsLat
     *
     * @return string
     */
    public function getCoordsLat()
    {
        return $this->coordsLat;
    }

    /**
     * Set coordsLong
     *
     * @param string $coordsLong
     * @return Event
     */
    public function setCoordsLong($coordsLong)
    {
        $this->coordsLong = $coordsLong;

        return $this;
    }

    /**
     * Get coordsLong
     *
     * @return string
     */
    public function getCoordsLong()
    {
        return $this->coordsLong;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Event
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return Event
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set collection
     *
     * @param integer $collection
     * @return Event
     */
    public function setCollection($collection)
    {
        $this->collection = $collection;

        return $this;
    }

    /**
     * Get collection
     *
     * @return EventCollection
     */
    public function getCollection()
    {
        return $this->collection;
    }
}
