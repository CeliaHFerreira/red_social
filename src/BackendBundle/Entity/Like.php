<?php

namespace BackendBundle\Entity;

/**
 * Like
 */
class Like
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var \BackendBundle\Entity\Melody
     */
    private $melody;

    /**
     * @var \BackendBundle\Entity\User
     */
    private $user;


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
     * Set melody
     *
     * @param \BackendBundle\Entity\Melody $melody
     *
     * @return Like
     */
    public function setMelody(\BackendBundle\Entity\Melody $melody = null)
    {
        $this->melody = $melody;

        return $this;
    }

    /**
     * Get melody
     *
     * @return \BackendBundle\Entity\Melody
     */
    public function getMelody()
    {
        return $this->melody;
    }

    /**
     * Set user
     *
     * @param \BackendBundle\Entity\User $user
     *
     * @return Like
     */
    public function setUser(\BackendBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \BackendBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }
}
