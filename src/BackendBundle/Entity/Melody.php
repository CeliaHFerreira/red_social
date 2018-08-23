<?php
/**
 * Melody, entity
 */
namespace BackendBundle\Entity;

/**
 * Melody
 */
class Melody
{
    /**
	 * id melody
	 * 
     * @var integer
     */
    private $id;

    /**
	 * name melody
	 * 
     * @var string
     */
    private $name;

    /**
	 * melody
	 * 
     * @var string
     */
    private $melody;

    /**
     * @var string
     */
    private $image;

    /**
	 * status melody
	 * 
     * @var string
     */
    private $status;

    /**
	 * creation melody
	 * 
     * @var \DateTime
     */
    private $creationDate;

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
     * Set name
     *
     * @param string $name
     *
     * @return Melody
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
     * Set melody
     *
     * @param string $melody
     *
     * @return Melody
     */
    public function setMelody($melody)
    {
        $this->melody = $melody;

        return $this;
    }

    /**
     * Get melody
     *
     * @return string
     */
    public function getMelody()
    {
        return $this->melody;
    }

    /**
     * Set image
     *
     * @param string $image
     *
     * @return Melody
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set status
     *
     * @param string $status
     *
     * @return Melody
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set creationDate
     *
     * @param \DateTime $creationDate
     *
     * @return Melody
     */
    public function setCreationDate($creationDate)
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    /**
     * Get creationDate
     *
     * @return \DateTime
     */
    public function getCreationDate()
    {
        return $this->creationDate;
    }

    /**
     * Set user
     *
     * @param \BackendBundle\Entity\User $user
     *
     * @return Melody
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
