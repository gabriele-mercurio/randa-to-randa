<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="revenue_costs")
 */
class RevenueCost
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
     */
    private $id;

    /** @ORM\Column(type="string", length=32) */
    //
    private $type;

    /** @ORM\Column(type="integer") */
    private $value;

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

    /** Get the value of type */
    public function getType()
    {
        return $this->type;
    }

    /** Set the value of type */
    public function setType($type): self
    {
        $this->type = $type;
        return $this;
    }

    /** Get the value of value */
    public function getValue()
    {
        return $this->value;
    }

    /** Set the value of value */
    public function setValue($value): self
    {
        $this->value = $value;
        return $this;
    }
}
