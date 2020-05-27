<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\StrategyRepository")
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

    /** @ORM\ManyToOne(targetEntity="Target", cascade={"all"}, fetch="LAZY") */
    private $target;

    /** @ORM\Column(type="text") */
    private $description;

    /** @ORM\Column(type="date") */
    private $timestamp;

    /**
     * Strategy constructor.
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
    public function getTarget(): Target
    {
        return $this->target;
    }

    /** Set the value of target */
    public function setTarget(Target $target): self
    {
        $this->target = $target;
        return $this;
    }

    /** Get the value of description */
    public function getDescription(): string
    {
        return $this->description;
    }

    /** Set the value of description */
    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    /** Get the value of timestamp */
    public function getTimestamp(): DateTime
    {
        return $this->timestamp;
    }

    /** Set the value of timestamp */
    public function setTimestamp(DateTime $timestamp): self
    {
        $this->timestamp = $timestamp;
        return $this;
    }
}
