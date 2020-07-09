<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RandaRepository")
 * @ORM\Table(name="randa")
 */
class Randa
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
     */
    private $id;

    /** @ORM\Column(type="integer", length=4) */
    private $year;

    /**
     * @ORM\ManyToOne(targetEntity="Region", cascade={"all"}, fetch="LAZY")
     * @ORM\JoinColumn(nullable=false)
     */
    private $region;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $currentState;

    /** @ORM\Column(name="current_timeslot", type="string", options={"default":"T0"}) */
    private $currentTimeslot;

    /**
     *  @ORM\Column(name="note", type="string") 
     * @ORM\JoinColumn(nullable=true) 
     */
    private $note;


    /**
     *  @ORM\Column(name="refuse_note", type="string") 
     */
    private $refuseNote;



    /** 
     * @ORM\Column(name="directors_previsions", type="string") 
     * @ORM\JoinColumn(nullable=true) 
     */
    private $directorsPrevisions;

    /**
     * @var Collection|Economic[]
     *
     * @ORM\OneToMany(targetEntity="Economic", mappedBy="randa")
     */
    private $economics;

    /**
     * @var Collection|Rana[]
     *
     * @ORM\OneToMany(targetEntity="Rana", mappedBy="randa")
     */
    private $ranas;

    /**
     * @var Collection|StrategyPerRanda[]
     *
     * @ORM\OneToMany(targetEntity="StrategyPerRanda", mappedBy="randa")
     */
    private $strategiesPerRanda;

    /**
     * Randa constructor.
     *
     * @throws Exception
     */
    public function __construct()
    {
        $this->id = Uuid::uuid4();
    }

    /** Get the value of id */
    public function getId(): string
    {
        return $this->id->toString();
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

    /** Get the value of region */
    public function getRegion(): Region
    {
        return $this->region;
    }

    /** Set the value of region */
    public function setRegion(Region $region): self
    {
        $this->region = $region;
        return $this;
    }

    /** Get the value of currentTimeslot */
    public function getCurrentTimeslot(): string
    {
        return $this->currentTimeslot;
    }


    /** Get the value of currentState */
    public function getCurrentState(): string
    {
        return $this->currentState ? $this->currentState : "TODO";
    }

    /** Set the value of note */
    public function setNote(string $note): self
    {
        $this->note = $note;
        return $this;
    }


    /** Set the value of refuse note */
    public function setRefuseNote(string $refuseNote): self
    {
        $this->refuseNote = $refuseNote;
        return $this;
    }

    /** Get the value of directorsPrevisions\ */
    public function getDirectorsPrevisions(): string
    {
        if (!$this->directorsPrevisions) return "";
        return $this->directorsPrevisions;
    }

    /** Set the value of note */
    public function setDirectorsPrevisions(string $directorsPrevisions): self
    {
        $this->directorsPrevisions = $directorsPrevisions;
        return $this;
    }

    /** Get the value of note */
    public function getNote()
    {
        return $this->note;
    }
    /** Get the value of note */
    public function getRefuseNote()
    {
        return $this->refuseNote;
    }

    /** Set the value of currentTimeslot */
    public function setCurrentTimeslot(string $currentTimeslot): self
    {
        $this->currentTimeslot = $currentTimeslot;
        return $this;
    }


    /** Set the value of currentState */
    public function setCurrentState(string $currentState): self
    {
        $this->currentState = $currentState;
        return $this;
    }


    /**
     * @return Collection|Economic[]
     */
    public function getEconomics()
    {
        return $this->economics;
    }

    /**
     * @return Collection|Rana[]
     */
    public function getRanas()
    {
        return $this->ranas;
    }

    /**
     * @return Collection|StrategyPerRanda[]
     */
    public function getStrategiesPerRanda()
    {
        return $this->strategiesPerRanda;
    }
}
