<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RanaLifecycleRepository")
 * @ORM\Table(name="rana_lifecycle")
 */
class RanaLifecycle
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Rana", cascade={"all"}, fetch="LAZY")
     * @ORM\JoinColumn(nullable=false)
     */
    private $rana;


    /** @ORM\Column(name="current_timeslot", type="string", length=2) */
    private $currentTimeslot;


    /** @ORM\Column(name="current_status", type="string", length=8, options={"default"="TODO"}) */
    private $currentState;

    /**
     * RanaLifecycle constructor.
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

    /** Get the value of rana */
    public function getRana(): Rana
    {
        return $this->rana;
    }

    /** Set the value of rana */
    public function setRana(Rana $rana): self
    {
        $this->rana = $rana;
        return $this;
    }

    /** Get the value of currentTimeslot */
    public function getCurrentTimeslot(): string
    {
        return $this->currentTimeslot;
    }

    /** Set the value of currentTimeslot */
    public function setCurrentTimeslot(string $currentTimeslot): self
    {
        $this->currentTimeslot = $currentTimeslot;
        return $this;
    }

    /** Get the value of currentState */
    public function getCurrentState(): string
    {
        return $this->currentState;
    }

    /** Set the value of currentState */
    public function setCurrentState(string $currentState): self
    {
        $this->currentState = $currentState;
        return $this;
    }
}
