<?php
/*
 * Score, entity
 */
namespace BackendBundle\Entity;

/**
 * Score
 */
class Score
{
    /**
	 * id score
	 * 
     * @var integer
     */
    private $id;

    /**
	 * score
	 * 
     * @var integer
     */
    private $score;

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
     * Set score
     *
     * @param integer $score
     *
     * @return Score
     */
    public function setScore($score)
    {
        $this->score = $score;

        return $this;
    }

    /**
     * Get score
     *
     * @return integer
     */
    public function getScore()
    {
        return $this->score;
    }

    /**
     * Set melody
     *
     * @param \BackendBundle\Entity\Melody $melody
     *
     * @return Score
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
     * @return Score
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
    /**
     * @var integer
     */
    private $ponderedScore;


    /**
     * Set ponderedScore
     *
     * @param integer $ponderedScore
     *
     * @return Score
     */
    public function setPonderedScore($ponderedScore)
    {
        $this->ponderedScore = $ponderedScore;

        return $this;
    }

    /**
     * Get ponderedScore
     *
     * @return integer
     */
    public function getPonderedScore()
    {
        return $this->ponderedScore;
    }
}
