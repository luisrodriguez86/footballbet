<?php

namespace Football\FootballbetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;


/**
 * Publicity
 *
 * @ORM\Table(name="publicity")
 * @ORM\Entity(repositoryClass="Football\FootballbetBundle\Entity\PublicityRepository")
 */
class Publicity
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="photo", type="string", length=255, nullable=false)
     */
    private $photo;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="date", nullable=false)
     */
    private $date;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="expiredate", type="date", nullable=false)
     */
    private $expiredate;

    private $file;





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
     * Set name
     *
     * @param string $name
     * @return Publicity
     */
    public function setName($name)
    {
        $this->name = $name;
    
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set photo
     *
     * @param string $photo
     * @return Publicity
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
     * Set notice
     *
     * @param string $notice
     * @return Publicity
     */
    public function setNotice($notice)
    {
        $this->notice = $notice;
    
        return $this;
    }

    /**
     * Get notice
     *
     * @return string 
     */
    public function getNotice()
    {
        return $this->notice;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return Publicity
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

    /**
     * Set expiredate
     *
     * @param \DateTime $expiredate
     * @return Publicity
     */
    public function setExpiredate($expiredate)
    {
        $this->expiredate = $expiredate;
    
        return $this;
    }

    /**
     * Get expiredate
     *
     * @return \DateTime 
     */
    public function getExpiredate()
    {
        return $this->expiredate;
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
        return 'slider-elastic';
    }

    public function upload()
    {
        $filename = md5($this->getId()).".jpeg";

        $this->getFile()->move(
            $this->getUploadRootDir(),$filename);

        $this->photo = $filename;
    }

    public function removeUpload()
    {
        if ($this->getPhoto()!='') {
            unlink($this->getAbsolutePath());
        }
    }
}