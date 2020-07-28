<?php

namespace App\Entity;

use App\Repository\StatesRepository;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;

/**
 * @ORM\Entity(repositoryClass=StatesRepository::class)
 */
class States
{

    /**
     * Members constructor.
     *
     * @throws Exception
     */
    public function __construct()
    {
        $this->id = Uuid::uuid4();
    }

    /**
     * @ORM\Id()
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity=chapter::class, inversedBy="states", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $chapter;

    /**
     * @ORM\Column(type="integer")
     */
    private $year;

    /**
     * @ORM\Column(type="string")
     */
    private $m1;

    /**
     * @ORM\Column(type="string")
     */
    private $m2;

    /**
     * @ORM\Column(type="string")
     */
    private $m3;

    /**
     * @ORM\Column(type="string")
     */
    private $m4;

    /**
     * @ORM\Column(type="string")
     */
    private $m5;

    /**
     * @ORM\Column(type="string")
     */
    private $m6;

    /**
     * @ORM\Column(type="string")
     */
    private $m7;

    /**
     * @ORM\Column(type="string")
     */
    private $m8;

    /**
     * @ORM\Column(type="string")
     */
    private $m9;

    /**
     * @ORM\Column(type="string")
     */
    private $m10;

    /**
     * @ORM\Column(type="string")
     */
    private $m11;

    /**
     * @ORM\Column(type="string")
     */
    private $m12;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getChapter(): ?chapter
    {
        return $this->chapter;
    }

    public function setChapter(chapter $chapter): self
    {
        $this->chapter = $chapter;

        return $this;
    }

    public function getYear(): ?string
    {
        return $this->year;
    }

    public function setYear(string $year): self
    {
        $this->year = $year;

        return $this;
    }

    public function getM1(): ?string
    {
        return $this->m1;
    }

    public function setM1(string $m1): self
    {
        $this->m1 = $m1;

        return $this;
    }

    public function getM2(): ?string
    {
        return $this->m2;
    }

    public function setM2(string $m2): self
    {
        $this->m2 = $m2;

        return $this;
    }

    public function getM3(): ?string
    {
        return $this->m3;
    }

    public function setM3(string $m3): self
    {
        $this->m3 = $m3;

        return $this;
    }

    public function getM4(): ?string
    {
        return $this->m4;
    }

    public function setM4(string $m4): self
    {
        $this->m4 = $m4;

        return $this;
    }

    public function getM5(): ?string
    {
        return $this->m5;
    }

    public function setM5(string $m5): self
    {
        $this->m5 = $m5;

        return $this;
    }

    public function getM6(): ?string
    {
        return $this->m6;
    }

    public function setM6(string $m6): self
    {
        $this->m6 = $m6;

        return $this;
    }

    public function getM7(): ?string
    {
        return $this->m7;
    }

    public function setM7(string $m7): self
    {
        $this->m7 = $m7;

        return $this;
    }

    public function getM8(): ?string
    {
        return $this->m8;
    }

    public function setM8(string $m8): self
    {
        $this->m8 = $m8;

        return $this;
    }

    public function getM9(): ?string
    {
        return $this->m9;
    }

    public function setM9(string $m9): self
    {
        $this->m9 = $m9;

        return $this;
    }

    public function getM10(): ?string
    {
        return $this->m10;
    }

    public function setM10(string $m10): self
    {
        $this->m10 = $m10;

        return $this;
    }

    public function getM11(): ?string
    {
        return $this->m11;
    }

    public function setM11(string $m11): self
    {
        $this->m11 = $m11;

        return $this;
    }

    public function getM12(): ?string
    {
        return $this->m12;
    }

    public function setM12(string $m12): self
    {
        $this->m12 = $m12;

        return $this;
    }

    public function setMonth($m, $v)
    {
        switch ($m) {
            case 1:
                $this->m1 = $v;
                break;
            case 2:
                $this->m2 = $v;
                break;
            case 3:
                $this->m3 = $v;
                break;
            case 4:
                $this->m4 = $v;
                break;
            case 5:
                $this->m5 = $v;
                break;
            case 6:
                $this->m6 = $v;
                break;
            case 7:
                $this->m7 = $v;
                break;
            case 8:
                $this->m8 = $v;
                break;
            case 9:
                $this->m9 = $v;
                break;
            case 10:
                $this->m10 = $v;
                break;
            case 11:
                $this->m11 = $v;
                break;
            case 12:
                $this->m12 = $v;
                break;
        }
    }
}
