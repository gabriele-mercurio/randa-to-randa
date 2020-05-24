<?php

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="rana_lifecycle")
 */
class RanaLifecycle
{
    /** 
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY") 
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Rana", cascade={"all"}, fetch="LAZY")
     * @ORM\JoinColumn(nullable=false)
     */
    private $rana;


    /** @ORM\Column(type="string", length=2) */
    // T0 | T1 | T2 | T3 | T4
    private $current_timeslot;


    /** @ORM\Column(type="string", length=8, options={"default"="TODO"}) */
    // TODO | PROPOSED | APPROVED | REFUSED
    private $current_status;


    /**
     * Get the value of id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get the value of rana
     */
    public function getRana()
    {
        return $this->rana;
    }

    /**
     * Set the value of rana
     *
     * @return  self
     */
    public function setRana($rana)
    {
        $this->randa = $rana;

        return $this;
    }

    

    /**
     * Get the value of current_timeslot
     */ 
    public function getCurrent_timeslot()
    {
        return $this->current_timeslot;
    }

    /**
     * Set the value of current_timeslot
     *
     * @return  self
     */ 
    public function setCurrent_timeslot($current_timeslot)
    {
        $this->current_timeslot = $current_timeslot;

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
