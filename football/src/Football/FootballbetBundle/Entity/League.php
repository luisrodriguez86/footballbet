<?php

namespace Football\FootballbetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * League
 *
 * @ORM\Table(name="league")
 * @ORM\Entity
 */
class League
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
     * @ORM\Column(name="nombre", type="string", length=255)
     */
    private $nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="logo", type="text")
     */
    private $logo;

    /** @ORM\OneToMany(targetEntity="Football\FootballbetBundle\Entity\League", mappedBy="league") */
    private $games;

    /** @ORM\OneToMany(targetEntity="Football\FootballbetBundle\Entity\Grupo", mappedBy="league") */
    private $grupos;

    public function __construct()
    {
          $this->logo = "";
          $this->games = new \Doctrine\Common\Collections\ArrayCollection();
        $this->grupos = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set nombre
     *
     * @param string $nombre
     * @return League
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get nombre
     *
     * @return string
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Set logo
     *
     * @param string $logo
     * @return League
     */
    public function setLogo($logo)
    {
        $this->logo = $logo;

        return $this;
    }

    /**
     * Get logo
     *
     * @return string
     */
    public function getLogo()
    {
        return $this->logo;
    }

    /**
     * Add grupo
     *
     * @param \Football\FootballbetBundle\Entity\Grupo $usergrupos
     * @return League
     */
    public function addGrupo(Grupo $grupo)
    {
        $this->grupos[] = $grupo;

        return $this;
    }

    /**
     * Remove grupo
     *
     * @param \Football\FootballbetBundle\Entity\Grupo $grupo
     */
    public function removeGrupo(Grupo $grupo)
    {
        $this->grupos->removeElement($grupo);
    }

    /**
     * Get grupos
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getGrupos()
    {
        return $this->grupos;
    }

    public function __toString()
    {

        return $this->nombre;

    }
}