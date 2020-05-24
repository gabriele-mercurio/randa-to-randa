<?php

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="rana")
 */
class Rana
{
    /** 
     * @ORM\Id
     * @ORM\Column(type="integer") 
     * @ORM\GeneratedValue(strategy="IDENTITY") */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="Chapter", cascade={"all"}, fetch="LAZY")
     * @ORM\JoinColumn(nullable=false)
     */
    private $chapter;

    /**
     * @ORM\ManyToOne(targetEntity="Randa", cascade={"all"}, fetch="LAZY")
     * @ORM\JoinColumn(nullable=false)
     */
    private $randa;

     
    /**
     * Get the value of id
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * Get the value of chapter
     */
    public function getChapter()
    {
        return $this->chapter;
    }

    /**
     * Set the value of chapter
     *
     * @return  self
     */
    public function setChapter($chapter)
    {
        $this->chapter = $chapter;

        return $this;
    }

    /**
     * Get the value of randa
     */
    public function getRanda()
    {
        return $this->randa;
    }

    /**
     * Set the value of randa
     *
     * @return  self
     */
    public function setRanda($randa)
    {
        $this->randa = $randa;

        return $this;
    }

    
    /**
     * Get the value of current_status
     */
    public function getCurrent_status()
    {
        return $this->current_status;
    }

    /**
     * Set the value of current_status
     *
     * @return  self
     */
    public function setCurrent_status($current_status)
    {
        $this->current_status = $current_status;

        return $this;
    }
}
