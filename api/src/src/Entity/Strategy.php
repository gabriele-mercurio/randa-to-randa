<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="strategies")
 */
class Strategy
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     * @ORM\ManyToOne(targetEntity="Target", cascade={"all"}, fetch="LAZY")
     */
    private $target;

    /** @ORM\Column(type="text") */
    private $description;

    /** @ORM\Column(type="date") */
    private $timestap;

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

    /** Get the value of target */
    public function getTarget()
    {
        return $this->target;
    }

    /** Set the value of target */
    public function setTarget($target): self
    {
        $this->target = $target;
        return $this;
    }

    /** Get the value of description */
    public function getDescription()
    {
        return $this->description;
    }

    /** Set the value of description */
    public function setDescription($description): self
    {
        $this->description = $description;
        return $this;
    }

    /** Get the value of timestap */
    public function getTimestap()
    {
        return $this->timestap;
    }

    /** Set the value of timestap */
    public function setTimestap($timestap): self
    {
        $this->timestap = $timestap;
        return $this;
    }
}
