<?php

namespace Football\FootballbetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Games
 *
 * @ORM\Table(name="games")
 * @ORM\Entity(repositoryClass="Football\FootballbetBundle\Entity\GamesRepository")
 */
class Games
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /** @ORM\ManyToOne(targetEntity="Football\FootballbetBundle\Entity\Team") */
    private $local;

    /** @ORM\ManyToOne(targetEntity="Football\FootballbetBundle\Entity\Team") */
    private $away;

    /** @ORM\ManyToOne(targetEntity="Football\FootballbetBundle\Entity\Season") */
    private $season;

    /** @ORM\ManyToOne(targetEntity="Football\FootballbetBundle\Entity\League", inversedBy="games") */
    private $league;

    /**
     * @var integer
     *
     * @ORM\Column(name="local_goals", type="integer")
     */
    private $localGoals;

    /**
     * @var integer
     *
     * @ORM\Column(name="away_goals", type="integer")
     */
    private $awayGoals;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var boolean
     *
     * @ORM\Column(name="updated", type="boolean")
     */
    private $updated;

    public function __construct()
    {
        $this->updated = false;
    }
    /**
     * @param mixed $away
     */
    public function setAway($away)
    {
        $this->away = $away;
    }

    /**
     * @return mixed
     */
    public function getAway()
    {
        return $this->away;
    }

    /**
     * @param int $awayGoals
     */
    public function setAwayGoals($awayGoals)
    {
        $this->awayGoals = $awayGoals;
    }

    /**
     * @return int
     */
    public function getAwayGoals()
    {
        return $this->awayGoals;
    }

    /**
     * @param \DateTime $date
     */
    public function setDate($date)
    {
        $this->date = $date;
    }

    /**
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $local
     */
    public function setLocal($local)
    {
        $this->local = $local;
    }

    /**
     * @return mixed
     */
    public function getLocal()
    {
        return $this->local;
    }

    /**
     * @param int $localGoals
     */
    public function setLocalGoals($localGoals)
    {
        $this->localGoals = $localGoals;
    }

    /**
     * @return int
     */
    public function getLocalGoals()
    {
        return $this->localGoals;
    }

    /**
     * @return mixed
     */
    public function getSeason()
    {
        return $this->season;
    }

    /**
     * @param mixed $season
     */
    public function setSeason($season)
    {
        $this->season = $season;
    }

    /**
     * @return mixed
     */
    public function getLeague()
    {
        return $this->league;
    }

    /**
     * @param mixed $season
     */
    public function setLeague($league)
    {
        $this->league = $league;
    }

    public function __toString()
    {

        return "$this->local VS $this->away";

    }

    /**
     * Set updated
     *
     * @param boolean $updated
     * @return Games
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;
    
        return $this;
    }

    /**
     * Get updated
     *
     * @return boolean 
     */
    public function getUpdated()
    {
        return $this->updated;
    }
}