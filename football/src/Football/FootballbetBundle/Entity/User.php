<?php

namespace Football\FootballbetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Form\Extension\Core\DataTransformer\IntegerToLocalizedStringTransformer;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToMany;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

use Football\FootballbetBundle\Entity\Notificacion;
use Football\FootballbetBundle\Entity\UserGrupo;
use Football\FootballbetBundle\Entity\Group;

/**
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="Football\FootballbetBundle\Entity\UserRepository")
 * @UniqueEntity(fields="email", message="Email ya en uso.")
 *
 */

class User implements AdvancedUserInterface, \Serializable {

	
	/**
	 * @ORM\Column(type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;

	/**
	 * @ORM\Column(type="string", length=32)
	 */
	private $nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="photo", type="text")
     */
    private $photo;
	
	/**
	 * @ORM\Column(type="string", length=32)
	 */
	private $salt;
	
	/**
	 * @ORM\Column(type="string", length=40)
	 */
	private $password;
	
	/**
	 * @ORM\Column(type="string", unique=true)
	 */
	private $email;
	
	/**
	 * @ORM\Column(type="string", length=40, name="register_code")
	 */
	private $registerCode;
	
	/**
	 * @ORM\Column(name="is_active", type="boolean",nullable=True)
	 */
	private $isActive;
	
	/**
	 * @ORM\ManyToMany(targetEntity="Group", inversedBy="users")
	 *
	 */
	private $groups;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     */
    private $points=0;

    /**
     * @ORM\OneToMany(targetEntity="Football\FootballbetBundle\Entity\UserGrupo", mappedBy="user")
     */
    private $usergrupos;

    /**
     *
     * @ORM\OneToMany(targetEntity="Football\FootballbetBundle\Entity\Notificacion",mappedBy="user")
     */
    private $notificaciones;

    private $file;


	public function isAccountNonExpired() {
		return true;

	}
	public function isAccountNonLocked() {
		return true;

	}
	public function isCredentialsNonExpired() {
		return true;

	}
	public function isEnabled() {
		return $this->isActive and $this->registerCode == '0';
	}
	
	/**
     * Set registerCode
     *
     * @param string $registerCode
     */
    public function setRegisterCode($registerCode)
    {
        $this->registerCode = $registerCode;
    }

   	/**
	 * @inheritDoc
	 */
	public function getUsername()
	{
		return $this->email;
	}

	/**
	 * @inheritDoc
	 */
	public function getRoles()
	{
		return $this->groups->toArray();
	}
	
	/**
	 * @inheritDoc
	 */
	public function eraseCredentials()
	{
	}
	
	/**
	 * @inheritDoc
	 */
	public function equals(UserInterface $user)
	{
		return $this->email === $user->getEmail();
	}
	
	/**
	 * Serializes the content of the current User object
	 * @return string
	 */
	public function serialize()
	{
		return \json_encode(
				array($this->email, $this->password, $this->salt,
						$this->groups, $this->id));
	}
	
	/**
	 * Unserializes the given string in the current User object
	 * @param serialized
	 */
	public function unserialize($serialized)
	{
		list($this->email, $this->password, $this->salt,
				$this->groups, $this->id) = \json_decode(
						$serialized);
	}
   
    public function __construct()
    {
    	$this->isActive = true;
        $this->salt = md5(uniqid(null, true));
        $this->photo = "";
        $this->groups = new \Doctrine\Common\Collections\ArrayCollection();
        $this->usergrupos = new \Doctrine\Common\Collections\ArrayCollection();
        $this->notificaciones = new \Doctrine\Common\Collections\ArrayCollection();
        $this->registerCode = "0";
        $this->points = 0;

    }
    
    public function __toString()
    {
        if(is_null($this->email)) {
            return 'NULL';
        }
        return $this->email;
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
     * @return User
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
     * @inheritDoc
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * Set salt
     *
     * @param string $salt
     * @return User
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     * @return User
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean 
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Add groups
     *
     * @param \Football\FootballbetBundle\Entity\Group $groups
     * @return User
     */
    public function addGroup(Group $groups)
    {
        $this->groups[] = $groups;

        return $this;
    }

    /**
     * Remove groups
     *
     * @param \Football\FootballbetBundle\Entity\Group $groups
     */
    public function removeGroup(Group $groups)
    {
        $this->groups->removeElement($groups);
    }

    /**
     * Get groups
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getGroups()
    {
        return $this->groups;
    }

    /**
     * Set groups
     *
     * @param \Doctrine\Common\Collections\Collection
     */
    public function setGroups($groups)
    {
        $this->groups = $groups;
    }
    /**
     * Get registerCode
     *
     * @return string 
     */
    public function getRegisterCode()
    {
        return $this->registerCode;
    }

    /**
     * Set photo
     *
     * @param string $photo
     * @return User
     */
    public function setPhoto($photo)
    {
        $this->photo = $photo;
    
        return $this;
    }

    /**
     * Get photo
     *
     * @return string 
     */
    public function getPhoto()
    {
        return $this->photo;
    }

    /**
     * Set points
     *
     * @param integer $points
     * @return User
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
     * Add usergrupos
     *
     * @param \Football\FootballbetBundle\Entity\User $usergrupos
     * @return User
     */
    public function addUsergrupo(UserGrupo $usergrupos)
    {
        $this->usergrupos[] = $usergrupos;
    
        return $this;
    }

    /**
     * Remove usergrupos
     *
     * @param \Football\FootballbetBundle\Entity\User $usergrupos
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
     * Sets file.
     *
     * @param UploadedFile $file
     */
    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;
    }
    /**
     * Get file.
     *
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }



    public function getAbsolutePath()
    {
        return $this->getUploadRootDir().'/'.$this->photo;
    }

    public function getWebPath()
    {
        return null === $this->photo
            ? null
            : $this->getUploadDir().'/'.$this->photo;
    }

    protected function getUploadRootDir()
    {
        return __DIR__.'/../../../../web/bundles/footballbet/'.$this->getUploadDir();
    }

    protected function getUploadDir()
    {
        return 'uploads/users/avatars';
    }

    public function upload()
    {
        if($this->getFile()==null)
            return null;

        $this->removeUpload();

        $filename = md5(uniqid($this->getId())).".jpeg";

            $this->getFile()->move(
            $this->getUploadRootDir(),$filename);

        $this->photo = $filename;
    }

    public function removeUpload()
    {
        $ruta = $this->getAbsolutePath();
        if ($this->getPhoto()!='' && file_exists($ruta)) {
            unlink($ruta);
        }
    }

    /**
     * Add notificaciones
     *
     * @param \Football\FootballbetBundle\Entity\Notificacion $notificaciones
     * @return User
     */
    public function addNotificacione(Notificacion $notificaciones)
    {
        $this->notificaciones[] = $notificaciones;
    
        return $this;
    }

    /**
     * Remove notificaciones
     *
     * @param \Football\FootballbetBundle\Entity\Notificacion $notificaciones
     */
    public function removeNotificacione(Notificacion $notificaciones)
    {
        $this->notificaciones->removeElement($notificaciones);
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