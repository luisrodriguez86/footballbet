<?php

namespace Football\FootballbetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Football\FootballbetBundle\Entity\User;
use Football\FootballbetBundle\Entity\Grupo;

/**
 * Notificacion
 *
 * @ORM\Table(name="notificacionforgot")
 * @ORM\Entity
 */
class NotificacionForgot
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
     * @ORM\OneToOne(targetEntity="Football\FootballbetBundle\Entity\User")
     */
    private $user;

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
}