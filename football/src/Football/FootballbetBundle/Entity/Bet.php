<?php

namespace Football\FootballbetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Football\FootballbetBundle\Entity\Games;
use Football\FootballbetBundle\Entity\UserGrupo;

/**
 * Bet
 *
 * @ORM\Entity(repositoryClass="Football\FootballbetBundle\Entity\BetRepository")
 * @ORM\Table(name="bet")
 */
class Bet
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /** @ORM\ManyToOne(targetEntity="\Football\FootballbetBundle\Entity\Games")
     * @Assert\NotNull()
     */
    protected $game;

    /** @ORM\ManyToOne(targetEntity="\Football\FootballbetBundle\Entity\UserGrupo", inversedBy="bets")
     */
    protected $usergrupo;

    /**
     * @var boolean
     *
     * @ORM\Column(name="localwin", type="boolean")
     */

    protected $localwin;

    /**
     * @var boolean
     *
     * @ORM\Column(name="awaywin", type="boolean")
     */
    protected $awaywin;

    /**
     * @var boolean
     *
     * @ORM\Column(name="draft", type="boolean")
     */

    protected $draft;

    /**
     * @var integer
     *
     * @ORM\Column(name="points", type="integer")
     */
    protected $points;


    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    public function __construct()
    {
        $this->points = 0;
        $this->localwin = false;
        $this->awaywin = false;
        $this->draft = false;

        $this->date = new \DateTime();

    }

    /**
     * Set localwin
     *
     * @param boolean $localwin
     * @return Bet
     */
    public function setLocalwin($localwin)
    {
        $this->localwin = $localwin;

        return $this;
    }

    /**
     * Get localwin
     *
     * @return boolean
     */
    public function getLocalwin()
    {
        return $this->localwin;
    }

    /**
     * Set awaywin
     *
     * @param boolean $awaywin
     * @return Bet
     */
    public function setAwaywin($awaywin)
    {
        $this->awaywin = $awaywin;

        return $this;
    }

    /**
     * Get awaywin
     *
     * @return boolean
     */
    public function getAwaywin()
    {
        return $this->awaywin;
    }

    /**
     * Set game
     *
     * @param \Football\FootballbetBundle\Entity\Games $game
     * @return Bet
     */
    public function setGame(Games $game = null)
    {
        $this->game = $game;

        return $this;
    }

    /**
     * Get game
     *
     * @return \Football\FootballbetBundle\Entity\Games
     */
    public function getGame()
    {
        return $this->game;
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
     * Set draft
     *
     * @param boolean $draft
     * @return Bet
     */
    public function setDraft($draft)
    {
        $this->draft = $draft;
    
        return $this;
    }

    /**
     * Get draft
     *
     * @return boolean 
     */
    public function getDraft()
    {
        return $this->draft;
    }

    /**
     * Set points
     *
     * @param integer $points
     * @return Bet
     */
    public function setPoints($points)
    {
        $this->points = $points;
    
        return $this;
    }

    /**
     * Get points
     *
     * @return integer 
     */
    public function getPoints()
    {
        return $this->points;
    }  

    /**
     * Set usergrupo
     *
     * @param \Football\FootballbetBundle\Entity\UserGrupo $usergrupo
     * @return Bet
     */
    public function setUsergrupo(UserGrupo $usergrupo = null)
    {
        $this->usergrupo = $usergrupo;
    
        return $this;
    }

    /**
     * Get usergrupo
     *
     * @return \Football\FootballbetBundle\Entity\UserGrupo 
     */
    public function getUsergrupo()
    {
        return $this->usergrupo;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return Bet
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
}