<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;

/**
 * @ORM\Entity(repositoryClass="App\Repository\StrategyPerRandaRepository")
 * @ORM\Table(name="strategies_per_randa")
 */
class StrategyPerRanda
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
     */
    private $id;

    /** @ORM\ManyToOne(targetEntity="Randa", cascade={"all"}, fetch="LAZY") */
    private $randa;

    /** @ORM\ManyToOne(targetEntity="Strategy", cascade={"all"}, fetch="LAZY") */
    private $strategy;

    /**
     * StrategyPerRanda constructor.
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

    /** Get the value of strategy */
    public function getStrategy(): Strategy
    {
        return $this->strategy;
    }

    /** Set the value of strategy */
    public function setStrategy(Strategy $strategy): self
    {
        $this->strategy = $strategy;
        return $this;
    }
}
