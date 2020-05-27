<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="economics")
 */
class Economics
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
     */
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

    /** @ORM\Column(name="extra_incomings", type="integer") */
    private $extraIncomings;

    /** @ORM\Column(type="integer") */
    private $deprecations;

    /** @ORM\Column(type="integer") */
    private $provisions;

    /** @ORM\Column(name="financial_charges", type="integer") */
    private $financialCharges;

    /** @ORM\Column(type="integer") */
    private $tax;

    /**
     * User constructor.
     *
     * @throws Exception
     */
    public function __construct()
    {
        $this->id = Uuid::uuid4();
    }

    /** Get the value of id */
    public function getId(): UuidInterface
    {
        return $this->id;
    }

    /** Set the value of id */
    public function setId($id): self
    {
        $this->id = $id;
        return $this;
    }

    /** Get the value of randa */
    public function getRanda()
    {
        return $this->randa;
    }

    /** Set the value of randa */
    public function setRanda($randa): self
    {
        $this->randa = $randa;
        return $this;
    }

    /** Get the value of year */
    public function getYear()
    {
        return $this->year;
    }

    /** Set the value of year */
    public function setYear($year): self
    {
        $this->year = $year;
        return $this;
    }

    /** Get the value of timeslot */
    public function getTimeslot()
    {
        return $this->timeslot;
    }

    /** Set the value of timeslot */
    public function setTimeslot($timeslot): self
    {
        $this->timeslot = $timeslot;
        return $this;
    }

    /** Get the value of extraIncomings */
    public function getExtra_incomings()
    {
        return $this->extraIncomings;
    }

    /** Set the value of extraIncomings */
    public function setExtra_incomings($extraIncomings): self
    {
        $this->extraIncomings = $extraIncomings;
        return $this;
    }

    /** Get the value of deprecations */
    public function getDeprecations()
    {
        return $this->deprecations;
    }

    /** Set the value of deprecations */
    public function setDeprecations($deprecations): self
    {
        $this->deprecations = $deprecations;
        return $this;
    }

    /** Get the value of provisions */
    public function getProvisions()
    {
        return $this->provisions;
    }

    /** Set the value of provisions */
    public function setProvisions($provisions): self
    {
        $this->provisions = $provisions;
        return $this;
    }

    /** Get the value of financialCharges */
    public function getFinancial_charges()
    {
        return $this->financialCharges;
    }

    /** Set the value of financialCharges */
    public function setFinancial_charges($financialCharges): self
    {
        $this->financialCharges = $financialCharges;
        return $this;
    }

    /** Get the value of tax */
    public function getTax()
    {
        return $this->tax;
    }

    /** Set the value of tax */
    public function setTax($tax): self
    {
        $this->tax = $tax;
        return $this;
    }
}
