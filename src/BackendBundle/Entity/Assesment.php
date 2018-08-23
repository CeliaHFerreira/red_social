<?php
/*
 * Assesment, entity
 */
namespace BackendBundle\Entity;

/**
 * Assesment
 */
class Assesment
{
    /**
	 * id assest
	 * 
     * @var integer
     */
    private $id;

    /**
	 * text assest
	 * 
     * @var string
     */
    private $text;

    /**
	 * id melody
	 * 
     * @var \BackendBundle\Entity\Melody
     */
    private $melody;

    /**
	 * id user
	 * 
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
     * Set text
     *
     * @param string $text
     *
     * @return Assesment
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get text
     *
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set melody
     *
     * @param \BackendBundle\Entity\Melody $melody
     *
     * @return Assesment
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
     * @return Assesment
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
