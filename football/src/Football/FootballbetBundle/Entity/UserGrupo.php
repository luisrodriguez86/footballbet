<?php

namespace Football\FootballbetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Football\FootballbetBundle\Entity\Bet;
use Doctrine\Common\Collections\Collection;

/**
 * @ORM\Table(name="usergrupo")
 * @ORM\Entity
 */
class UserGrupo
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     * @var integer
     */
    private $points;

    /**
     * @ORM\ManyToOne(targetEntity="Football\FootballbetBundle\Entity\User", inversedBy="usergrupos")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="Football\FootballbetBundle\Entity\Grupo" , inversedBy="usergrupos")
     */
    private $grupo;

    /**
     * @ORM\OneToMany(targetEntity="Football\FootballbetBundle\Entity\Bet" , mappedBy="usergrupo", cascade={"remove"})
     */
    private $bets;

    public function __construct(){

        $this->points = 0;

        $this->bets = new \Doctrine\Common\Collections\ArrayCollection();

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
     * Set points
     *
     * @param integer $points
     * @return UserGrupo
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
     * Set user
     *
     * @param \Football\FootballbetBundle\Entity\User $user
     * @return UserGrupo
     */
    public function setUser(\Football\FootballbetBundle\Entity\User $user = null)
    {
        $this->user = $user;
    
        return $this;
    }

    /**
     * Get user
     *
     * @return \Football\FootballbetBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set grupo
     *
     * @param \Football\FootballbetBundle\Entity\Grupo $grupo
     * @return UserGrupo
     */
    public function setGrupo(\Football\FootballbetBundle\Entity\Grupo $grupo = null)
    {
        $this->grupo = $grupo;
    
        return $this;
    }

    /**
     * Get grupo
     *
     * @return \Football\FootballbetBundle\Entity\Grupo 
     */
    public function getGrupo()
    {
        return $this->grupo;
    }

    public function __toString(){

        return $this->user->getNombre()."-".$this->grupo->getNombre();

    }

    /**
     * Add bet
     *
     * @param \Football\FootballbetBundle\Entity\Bet $bet
     * @return UserGrupo
     */
    public function addBet(Bet $bet)
    {
        $this->bets[] = $bet;

        return $this;
    }

    /**
     * Remove bet
     *
     * @param \Football\FootballbetBundle\Entity\Bet $bet
     */
    public function removeBet(Bet $bet)
    {
        $this->bets->removeElement($bet);
    }

    /**
     * Get usergrupos
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBets()
    {
        return $this->bets;
    }
}