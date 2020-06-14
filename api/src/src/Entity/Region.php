<?php

namespace App\Entity;

use Doctrine\Common\Collections\Collection;
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

    /** @ORM\Column(type="string", length=50) */
    private $name;

    /** @ORM\Column(type="text", nullable=true) */
    private $notes;

    /**
     * @var Collection|Chapter[]
     *
     * @ORM\OneToMany(targetEntity="Chapter", mappedBy="region")
     */
    private $chapters;

    /**
     * @var Collection|Director[]
     *
     * @ORM\OneToMany(targetEntity="Director", mappedBy="region")
     */
    private $directors;

    /**
     * @var Collection|Randa[]
     *
     * @ORM\OneToMany(targetEntity="Randa", mappedBy="region")
     */
    private $randas;

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

    /**
     * @return Collection|Chapter[]
     */
    public function getChapters()
    {
        return $this->chapters;
    }

    /**
     * @return Collection|Director[]
     */
    public function getDirectors()
    {
        return $this->directors;
    }

    /**
     * @return Collection|Randa[]
     */
    public function getRandas()
    {
        return $this->randas;
    }
}
