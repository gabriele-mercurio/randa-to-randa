<?php

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="strategies_per_randa")
 */
class StrategiesPerRanda
{
    /** 
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY") */
    private $id;

    /** 
     *  @ORM\ManyToOne(targetEntity="Target", cascade={"all"}, fetch="LAZY")
     */
    private $randa;

     /** 
     *  @ORM\ManyToOne(targetEntity="Strategy", cascade={"all"}, fetch="LAZY")
     */
    private $strategy;


    /**
     * Get the value of id
     */ 
    public function getId()
    {
        return $this->id;
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
     * Get the value of strategy
     */ 
    public function getStrategy()
    {
        return $this->strategy;
    }

    /**
     * Set the value of strategy
     *
     * @return  self
     */ 
    public function setStrategy($strategy)
    {
        $this->strategy = $strategy;

        return $this;
    }
}