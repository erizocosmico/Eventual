<?php

namespace Eventual\Bundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * EventCollection
 *
 * @ORM\Table(name="eventual_event_collection")
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks()
 */
class EventCollection
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
     * @ORM\Column(name="public_key", type="string", length=255)
     */
    private $pubKey;

    /**
     * @var string
     *
     * @ORM\Column(name="private_key", type="string", length=255)
     */
    private $privateKey;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;

    /**
     * @ORM\OneToMany(targetEntity="Event", mappedBy="collection")
     */
    private $events;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="collections")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    public function __construct()
    {
        $this->events = new ArrayCollection();
    }

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
     * @return EventCollection
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
     * Set created
     *
     * @param \DateTime $created
     * @return EventCollection
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Add events
     *
     * @param \Eventual\Bundle\Entity\Event $events
     * @return EventCollection
     */
    public function addEvent(\Eventual\Bundle\Entity\Event $events)
    {
        $this->events[] = $events;

        return $this;
    }

    /**
     * Remove events
     *
     * @param \Eventual\Bundle\Entity\Event $events
     */
    public function removeEvent(\Eventual\Bundle\Entity\Event $events)
    {
        $this->events->removeElement($events);
    }

    /**
     * Get events
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getEvents()
    {
        return $this->events;
    }

    /**
     * Set user
     *
     * @param \Eventual\Bundle\Entity\User $user
     * @return EventCollection
     */
    public function setUser(\Eventual\Bundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Eventual\Bundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set pubKey
     *
     * @param string $pubKey
     * @return EventCollection
     */
    public function setPubKey($pubKey)
    {
        $this->pubKey = $pubKey;

        return $this;
    }

    /**
     * Get pubKey
     *
     * @return string
     */
    public function getPubKey()
    {
        return $this->pubKey;
    }

    /**
     * Set privateKey
     *
     * @param string $privateKey
     * @return EventCollection
     */
    public function setPrivateKey($privateKey)
    {
        $this->privateKey = $privateKey;

        return $this;
    }

    /**
     * Get privateKey
     *
     * @return string
     */
    public function getPrivateKey()
    {
        return $this->privateKey;
    }

    /**
     * @ORM\PrePersist
     */
    public function setupCollection()
    {
        $this->created = new \DateTime();
        $this->pubKey = md5(uniqid());
        $this->privateKey = md5(uniqid());
    }
}
