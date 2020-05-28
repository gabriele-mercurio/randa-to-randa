<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\EconomicRepository")
 * @ORM\Table(name="economics")
 */
class Economic
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

    /** @ORM\Column(type="integer") */
    private $year;

    /** @ORM\Column(type="string", length=2) */
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
     * Economic constructor.
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

    /** Get the value of randa */
    public function getRanda(): Randa
    {
        return $this->randa;
    }

    /** Set the value of randa */
    public function setRanda(Randa $randa): self
    {
        $this->randa = $randa;
        return $this;
    }

    /** Get the value of year */
    public function getYear(): int
    {
        return $this->year;
    }

    /** Set the value of year */
    public function setYear(int $year): self
    {
        $this->year = $year;
        return $this;
    }

    /** Get the value of timeslot */
    public function getTimeslot(): string
    {
        return $this->timeslot;
    }

    /** Set the value of timeslot */
    public function setTimeslot(string $timeslot): self
    {
        $this->timeslot = $timeslot;
        return $this;
    }

    /** Get the value of extraIncomings */
    public function getExtraIncomings(): int
    {
        return $this->extraIncomings;
    }

    /** Set the value of extraIncomings */
    public function setExtraIncomings(int $extraIncomings): self
    {
        $this->extraIncomings = $extraIncomings;
        return $this;
    }

    /** Get the value of deprecations */
    public function getDeprecations(): int
    {
        return $this->deprecations;
    }

    /** Set the value of deprecations */
    public function setDeprecations(int $deprecations): self
    {
        $this->deprecations = $deprecations;
        return $this;
    }

    /** Get the value of provisions */
    public function getProvisions(): int
    {
        return $this->provisions;
    }

    /** Set the value of provisions */
    public function setProvisions(int $provisions): self
    {
        $this->provisions = $provisions;
        return $this;
    }

    /** Get the value of financialCharges */
    public function getFinancialCharges(): int
    {
        return $this->financialCharges;
    }

    /** Set the value of financialCharges */
    public function setFinancialCharges(int $financialCharges): self
    {
        $this->financialCharges = $financialCharges;
        return $this;
    }

    /** Get the value of tax */
    public function getTax(): int
    {
        return $this->tax;
    }

    /** Set the value of tax */
    public function setTax(int $tax): self
    {
        $this->tax = $tax;
        return $this;
    }
}
