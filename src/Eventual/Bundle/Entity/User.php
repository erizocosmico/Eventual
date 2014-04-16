<?php

namespace Eventual\Bundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="eventual_user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\OneToMany(targetEntity="EventCollection", mappedBy="user")
     */
    private $collections;

    public function __construct()
    {
        parent::__construct();
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
     * Add collections
     *
     * @param \Eventual\Bundle\Entity\EventCollection $collections
     * @return User
     */
    public function addCollection(\Eventual\Bundle\Entity\EventCollection $collections)
    {
        $this->collections[] = $collections;

        return $this;
    }

    /**
     * Remove collections
     *
     * @param \Eventual\Bundle\Entity\EventCollection $collections
     */
    public function removeCollection(\Eventual\Bundle\Entity\EventCollection $collections)
    {
        $this->collections->removeElement($collections);
    }

    /**
     * Get collections
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCollections()
    {
        return $this->collections;
    }
}
