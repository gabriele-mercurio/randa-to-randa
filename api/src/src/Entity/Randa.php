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

    /** @ORM\Column(name="current_timeslot", type="string", options={"default":"T0"}) */
    private $currentTimeslot;

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

    /** Set the value of currentTimeslot */
    public function setCurrentTimeslot(string $currentTimeslot): self
    {
        $this->currentTimeslot = $currentTimeslot;
        return $this;
    }
}
