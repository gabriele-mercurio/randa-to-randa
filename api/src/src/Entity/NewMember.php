<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\NewMemberRepository")
 * @ORM\Table(name="new_members")
 */
class NewMember
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

    /** @ORM\Column(type="string", length=2) */
    private $timeslot;

    /** @ORM\Column(name="value_type", type="string", length=4) */
    private $valueType;

    /** @ORM\Column(type="integer", nullable=true) */
    private $m1;

    /** @ORM\Column(type="integer", nullable=true) */
    private $m2;

    /** @ORM\Column(type="integer", nullable=true) */
    private $m3;

    /** @ORM\Column(type="integer", nullable=true) */
    private $m4;

    /** @ORM\Column(type="integer", nullable=true) */
    private $m5;

    /** @ORM\Column(type="integer", nullable=true) */
    private $m6;

    /** @ORM\Column(type="integer", nullable=true) */
    private $m7;

    /** @ORM\Column(type="integer", nullable=true) */
    private $m8;

    /** @ORM\Column(type="integer", nullable=true) */
    private $m9;

    /** @ORM\Column(type="integer", nullable=true) */
    private $m10;

    /** @ORM\Column(type="integer", nullable=true) */
    private $m11;

    /** @ORM\Column(type="integer", nullable=true) */
    private $m12;

    /**
     * NewMember constructor.
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

    /** Get the value of valueType */
    public function getValueType(): string
    {
        return $this->valueType;
    }

    /** Set the value of valueType */
    public function setValueType(string $valueType): self
    {
        $this->valueType = $valueType;
        return $this;
    }

    /** Get the value of m1 */
    public function getM1(): int
    {
        return $this->m1;
    }

    /** Set the value of m1 */
    public function setM1(int $m1): self
    {
        $this->m1 = $m1;
        return $this;
    }

    /** Get the value of m2 */
    public function getM2(): int
    {
        return $this->m2;
    }

    /** Set the value of m2 */
    public function setM2(int $m2): self
    {
        $this->m2 = $m2;
        return $this;
    }

    /** Get the value of m3 */
    public function getM3(): int
    {
        return $this->m3;
    }

    /** Set the value of m3 */
    public function setM3(int $m3): self
    {
        $this->m3 = $m3;
        return $this;
    }

    /** Get the value of m4 */
    public function getM4(): int
    {
        return $this->m4;
    }

    /** Set the value of m4 */
    public function setM4(int $m4): self
    {
        $this->m4 = $m4;
        return $this;
    }

    /** Get the value of m5 */
    public function getM5(): int
    {
        return $this->m5;
    }

    /** Set the value of m5 */
    public function setM5(int $m5): self
    {
        $this->m5 = $m5;
        return $this;
    }

    /** Get the value of m6 */
    public function getM6(): int
    {
        return $this->m6;
    }

    /** Set the value of m6 */
    public function setM6(int $m6): self
    {
        $this->m6 = $m6;
        return $this;
    }

    /** Get the value of m7 */
    public function getM7(): int
    {
        return $this->m7;
    }

    /** Set the value of m7 */
    public function setM7(int $m7): self
    {
        $this->m7 = $m7;
        return $this;
    }

    /** Get the value of m8 */
    public function getM8(): int
    {
        return $this->m8;
    }

    /** Set the value of m8 */
    public function setM8(int $m8): self
    {
        $this->m8 = $m8;
        return $this;
    }

    /** Get the value of m9 */
    public function getM9(): int
    {
        return $this->m9;
    }

    /** Set the value of m9 */
    public function setM9(int $m9): self
    {
        $this->m9 = $m9;
        return $this;
    }

    /** Get the value of m10 */
    public function getM10(): int
    {
        return $this->m10;
    }

    /** Set the value of m10 */
    public function setM10(int $m10): self
    {
        $this->m10 = $m10;
        return $this;
    }

    /** Get the value of m11 */
    public function getM11(): int
    {
        return $this->m11;
    }

    /** Set the value of m11 */
    public function setM11(int $m11): self
    {
        $this->m11 = $m11;
        return $this;
    }

    /** Get the value of m12 */
    public function getM12(): int
    {
        return $this->m12;
    }

    /** Set the value of m12 */
    public function setM12(int $m12): self
    {
        $this->m12 = $m12;
        return $this;
    }

    /** Get the value of timeslot */
    public function getTimeslot(): string
    {
        return $this->timeslot;
    }

    /** Set the value of timeslot */
    public function setTimeslot(string $timeslot): self
    {
        $this->timeslot = $timeslot;
        return $this;
    }
}
