<?php

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="chapters")
 */
class Chapter
{
    /** 
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY") */
    private $id;
    
    /** @ORM\Column(type="string", length=32) */
    private $name;

    /** @ORM\Column(type="string", length=10, options={"default":"PROJECT"}) */
    private $current_state;

    /**
    * @ORM\ManyToOne(targetEntity="Region", cascade={"all"}, fetch="LAZY")
    * @ORM\JoinColumn(nullable=false)
    */
    private $region;

    /**
    * @ORM\ManyToOne(targetEntity="Director", cascade={"all"}, fetch="LAZY")
    * @ORM\JoinColumn(nullable=false)
    */
    private $director;


    /**
     * Get the value of id
     */ 
    public function getId()
    {
        return $this->id;
    }

   
    /**
     * Get the value of name
     */ 
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the value of name
     *
     * @return  self
     */ 
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get the value of current_state
     */ 
    public function getCurrent_state()
    {
        return $this->current_state;
    }

    /**
     * Set the value of current_state
     *
     * @return  self
     */ 
    public function setCurrent_state($current_state)
    {
        $this->current_state = $current_state;

        return $this;
    }

    /**
     * Get the value of region
     */ 
    public function getRegion()
    {
        return $this->region;
    }

    /**
     * Set the value of region
     *
     * @return  self
     */ 
    public function setRegion($region)
    {
        $this->region = $region;

        return $this;
    }

    /**
     * Get the value of director
     */ 
    public function getDirector()
    {
        return $this->director;
    }

    /**
     * Set the value of director
     *
     * @return  self
     */ 
    public function setDirector($director)
    {
        $this->director = $director;

        return $this;
    }
}
