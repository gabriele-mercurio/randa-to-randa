<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TargetRepository")
 * @ORM\Table(name="targets")
 */
class Target
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
     */
    private $id;

    /** @ORM\Column(type="string") */
    private $name;

    /**
     * @var Collection|Strategy[]
     *
     * @ORM\OneToMany(targetEntity="Strategy", mappedBy="target")
     */
    private $strategies;

    /**
     * Target constructor.
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

    /**
     * @return Collection|Strategy[]
     */
    public function getStrategies()
    {
        return $this->strategies;
    }
}
