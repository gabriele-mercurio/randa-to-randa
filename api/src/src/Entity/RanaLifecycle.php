<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity
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
    // T0 | T1 | T2 | T3 | T4
    private $currentTimeslot;


    /** @ORM\Column(name="current_status", type="string", length=8, options={"default"="TODO"}) */
    // TODO | PROPOSED | APPROVED | REFUSED
    private $currentStatus;

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

    /** Get the value of rana */
    public function getRana()
    {
        return $this->rana;
    }

    /** Set the value of rana */
    public function setRana($rana): self
    {
        $this->randa = $rana;
        return $this;
    }

    /** Get the value of currentTimeslot */
    public function getCurrentTimeslot()
    {
        return $this->currentTimeslot;
    }

    /** Set the value of currentTimeslot */
    public function setCurrentTimeslot($currentTimeslot): self
    {
        $this->currentTimeslot = $currentTimeslot;
        return $this;
    }

    /** Get the value of currentStatus */
    public function getCurrentStatus()
    {
        return $this->currentStatus;
    }

    /** Set the value of currentStatus */
    public function setCurrentStatus($currentStatus): self
    {
        $this->currentStatus = $currentStatus;
        return $this;
    }
}
