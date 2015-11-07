<?php

namespace Football\FootballbetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Team
 *
 * @ORM\Table(name="team")
 * @ORM\Entity
 */
class Team
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
     * @ORM\Column(name="nombre", type="text")
     */
    private $nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="logo", type="text")
     */
    private $logo;


    /** @ORM\ManyToOne(targetEntity="Football\FootballbetBundle\Entity\League") */
    protected $league;

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
     * @return Team
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
     * @return Team
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

    public function __toString()
    {
        return $this->getNombre();

    }

    /**
     * @param mixed $league
     */
    public function setLeague($league)
    {
        $this->league = $league;
    }

    /**
     * @return mixed
     */
    public function getLeague()
    {
        return $this->league;
    }
}