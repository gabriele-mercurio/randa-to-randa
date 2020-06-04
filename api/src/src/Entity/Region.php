<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RegionRepository")
 * @ORM\Table(name="regions")
 */
class Region
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
     */
    private $id;

    /** @ORM\Column(type="string", length=32) */
    private $name;

    /** @ORM\Column(type="text", nullable=true) */
    private $notes;

    /**
     * Region constructor.
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

    /** Get the value of name */
    public function getName(): string
    {
        return $this->name;
    }

    /** Set the value of name */
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /** Get the value of notes */
    public function getNotes(): ?string
    {
        return $this->notes;
    }

    /** Set the value of notes */
    public function setNotes(string $notes): self
    {
        $this->notes = $notes;
        return $this;
    }
}
