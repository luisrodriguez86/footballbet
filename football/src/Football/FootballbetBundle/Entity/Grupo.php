<?php

namespace Football\FootballbetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Football\FootballbetBundle\Entity\UserGrupo;
use Football\FootballbetBundle\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Grupo
 *
 * @ORM\Table(name="grupo")
 * @ORM\Entity(repositoryClass="Football\FootballbetBundle\Entity\GrupoRepository")
 */
class Grupo
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
     *  @Assert\NotBlank()
     * @ORM\Column(name="nombre", type="string", length=255)
     */
    private $nombre;

    /** @ORM\ManyToOne(targetEntity="Football\FootballbetBundle\Entity\League", inversedBy="grupos")
     *  @Assert\NotBlank()
     */
    private $league;

    /**
     * @ORM\OneToMany(targetEntity="Football\FootballbetBundle\Entity\UserGrupo" , mappedBy="grupo" , cascade={"remove"})
     */
    protected $usergrupos;

    /** @ORM\ManyToOne(targetEntity="Football\FootballbetBundle\Entity\User") */
    protected $group_admin;

    /** @ORM\OneToMany(targetEntity="Football\FootballbetBundle\Entity\Notificacion", mappedBy="grupo" , cascade={"remove"}) */
    protected $notificaciones;
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
     * @return Grupo
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
     * Set league
     *
     * @param string $league
     * @return Grupo
     */
    public function setLeague($league)
    {
        $this->league = $league;

        return $this;
    }

    /**
     * Get league
     *
     * @return string
     */
    public function getLeague()
    {
        return $this->league;
    }

    /**
     * Set group_admin
     *
     * @param \Football\FootballbetBundle\Entity\User $groupAdmin
     * @return Grupo
     */
    public function setGroupAdmin(User $groupAdmin = null)
    {
        $this->group_admin = $groupAdmin;
    
        return $this;
    }

    /**
     * Get group_admin
     *
     * @return \Football\FootballbetBundle\Entity\User 
     */
    public function getGroupAdmin()
    {
        return $this->group_admin;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->usergrupos = new \Doctrine\Common\Collections\ArrayCollection();
        $this->notificaciones = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Add usergrupos
     *
     * @param \Football\FootballbetBundle\Entity\UserGrupo $usergrupos
     * @return Grupo
     */
    public function addUsergrupo(UserGrupo $usergrupos)
    {
        $this->usergrupos[] = $usergrupos;
    
        return $this;
    }

    /**
     * Remove usergrupos
     *
     * @param \Football\FootballbetBundle\Entity\UserGrupo $usergrupos
     */
    public function removeUsergrupo(UserGrupo $usergrupos)
    {
        $this->usergrupos->removeElement($usergrupos);
    }

    /**
     * Get usergrupos
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUsergrupos()
    {
        return $this->usergrupos;
    }

    /**
     * Add usergrupos
     *
     * @param \Football\FootballbetBundle\Entity\Notificacion $notificacion
     * @return Grupo
     */
    public function addNotificaciones(Notificacion $notificacion)
    {
        $this->notificaciones[] = $notificacion;

        return $this;
    }

    /**
     * Remove usergrupos
     *
     * @param \Football\FootballbetBundle\Entity\Notificacion $notificacion
     */
    public function removeNotificaciones(Notificacion $notificacion)
    {
        $this->notificaciones->removeElement($notificacion);
    }

    /**
     * Get notificaciones
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getNotificaciones()
    {
        return $this->notificaciones;
    }
}