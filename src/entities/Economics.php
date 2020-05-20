<?php

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="economics")
 */
class Economics
{
    /** 
     * @ORM\Id
     * @ORM\Column(type="integer") 
     * @ORM\GeneratedValue(strategy="IDENTITY") */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Randa", cascade={"all"}, fetch="LAZY")
     * @ORM\JoinColumn(nullable=false)
     */
    private $randa;

    /**
     * @ORM\Column(type="integer")
     */
    private $year;

    /** @ORM\Column(type="string", length=2) */
    // T1 | T2 | T3 | T4
    private $timeslot;

    /**
     * @ORM\Column(type="integer")
     */
    private $extra_incomings;

    /**
     * @ORM\Column(type="integer")
     */
    private $deprecations;


    /**
     * @ORM\Column(type="integer")
     */
    private $provisions;


    /**
     * @ORM\Column(type="integer")
     */
    private $financial_charges;


    /**
     * @ORM\Column(type="integer")
     */
    private $tax;


    /**
     * Get the value of id
     */ 
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the value of id
     *
     * @return  self
     */ 
    public function setId($id)
    {
        $this->id = $id;

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
     * Get the value of year
     */ 
    public function getYear()
    {
        return $this->year;
    }

    /**
     * Set the value of year
     *
     * @return  self
     */ 
    public function setYear($year)
    {
        $this->year = $year;

        return $this;
    }

    /**
     * Get the value of timeslot
     */ 
    public function getTimeslot()
    {
        return $this->timeslot;
    }

    /**
     * Set the value of timeslot
     *
     * @return  self
     */ 
    public function setTimeslot($timeslot)
    {
        $this->timeslot = $timeslot;

        return $this;
    }

    /**
     * Get the value of extra_incomings
     */ 
    public function getExtra_incomings()
    {
        return $this->extra_incomings;
    }

    /**
     * Set the value of extra_incomings
     *
     * @return  self
     */ 
    public function setExtra_incomings($extra_incomings)
    {
        $this->extra_incomings = $extra_incomings;

        return $this;
    }

    /**
     * Get the value of deprecations
     */ 
    public function getDeprecations()
    {
        return $this->deprecations;
    }

    /**
     * Set the value of deprecations
     *
     * @return  self
     */ 
    public function setDeprecations($deprecations)
    {
        $this->deprecations = $deprecations;

        return $this;
    }

    /**
     * Get the value of provisions
     */ 
    public function getProvisions()
    {
        return $this->provisions;
    }

    /**
     * Set the value of provisions
     *
     * @return  self
     */ 
    public function setProvisions($provisions)
    {
        $this->provisions = $provisions;

        return $this;
    }

    /**
     * Get the value of financial_charges
     */ 
    public function getFinancial_charges()
    {
        return $this->financial_charges;
    }

    /**
     * Set the value of financial_charges
     *
     * @return  self
     */ 
    public function setFinancial_charges($financial_charges)
    {
        $this->financial_charges = $financial_charges;

        return $this;
    }

    /**
     * Get the value of tax
     */ 
    public function getTax()
    {
        return $this->tax;
    }

    /**
     * Set the value of tax
     *
     * @return  self
     */ 
    public function setTax($tax)
    {
        $this->tax = $tax;

        return $this;
    }
}