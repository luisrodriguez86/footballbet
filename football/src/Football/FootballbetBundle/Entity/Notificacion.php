<?php

namespace Football\FootballbetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Football\FootballbetBundle\Entity\User;
use Football\FootballbetBundle\Entity\Grupo;

/**
 * Notificacion
 *
 * @ORM\Table(name="notificacion")
 * @ORM\Entity
 */
class Notificacion
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
     *
     * @ORM\ManyToOne(targetEntity="Football\FootballbetBundle\Entity\User",inversedBy="notificaciones")
     */
    private $user;

    /**
     *
     * @ORM\ManyToOne(targetEntity="Football\FootballbetBundle\Entity\Grupo", inversedBy="notificaciones")
     */
    private $grupo;

    /**
     * @var string
     *
     * @ORM\Column(name="codigo", type="string", length=255)
     */
    private $codigo;


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
     * Set codigo
     *
     * @param string $codigo
     * @return Notificacion
     */
    public function setCodigo($codigo)
    {
        $this->codigo = $codigo;
    
        return $this;
    }

    /**
     * Get codigo
     *
     * @return string 
     */
    public function getCodigo()
    {
        return $this->codigo;
    }

    /**
     * Set user
     *
     * @param \Football\FootballbetBundle\Entity\User $user
     * @return Notificacion
     */
    public function setUser(User $user = null)
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
     * @return Notificacion
     */
    public function setGrupo(Grupo $grupo = null)
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
}