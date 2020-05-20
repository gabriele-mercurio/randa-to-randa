<?php

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="strategies")
 */
class StrategY
{
    /** 
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY") */
    private $id;

    /** @ORM\Column(type="string") 
     *  @ORM\ManyToOne(targetEntity="Target", cascade={"all"}, fetch="LAZY")
     */
    private $target;

     /** 
     * @ORM\Column(type="text") */
    private $description;

    /** 
     * @ORM\Column(type="date") */
    private $timestap;

    
    /**
     * Get the value of id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get the value of target
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * Set the value of target
     *
     * @return  self
     */
    public function setTarget($target)
    {
        $this->target = $target;

        return $this;
    }

    /**
     * Get the value of description
     */ 
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set the value of description
     *
     * @return  self
     */ 
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get the value of timestap
     */ 
    public function getTimestap()
    {
        return $this->timestap;
    }

    /**
     * Set the value of timestap
     *
     * @return  self
     */ 
    public function setTimestap($timestap)
    {
        $this->timestap = $timestap;

        return $this;
    }
}
