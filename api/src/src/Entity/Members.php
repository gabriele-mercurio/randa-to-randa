<?php

namespace App\Entity;

use App\Repository\MembersRepository;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
/**
 * @ORM\Entity(repositoryClass=MembersRepository::class)
 */
class Members
{
   /**
     * @ORM\Id()
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=4)
     */
    private $year;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $m1;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $m2;

     /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $m3;

     /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $m4;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $m5;

     /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $m6;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $m7;

     /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $m8;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $m9;

     /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $m10;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $m11;

     /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $m12;

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
     * @ORM\OneToOne(targetEntity=Chapter::class, inversedBy="members_history", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $chapter;

    public function getId(): ?string
    {
        return $this->id;
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

    public function getM1(): ?int
    {
        return $this->m1;
    }

    public function setM1(?int $m1): self
    {
        $this->m1 = $m1;

        return $this;
    }

    public function getM2(): ?int
    {
        return $this->m2;
    }

    public function setM2(?int $m2): self
    {
        $this->m2 = $m2;

        return $this;
    }

    public function getM3(): ?int
    {
        return $this->m3;
    }

    public function setM3(?int $m3): self
    {
        $this->m3 = $m3;

        return $this;
    }

    public function getM4(): ?int
    {
        return $this->m4;
    }

    public function setM4(?int $m4): self
    {
        $this->m4 = $m4;

        return $this;
    }

    public function getM5(): ?int
    {
        return $this->m5;
    }

    public function setM5(?int $m5): self
    {
        $this->m5 = $m5;

        return $this;
    }

    public function getM6(): ?int
    {
        return $this->m6;
    }

    public function setM6(?int $m6): self
    {
        $this->m6 = $m6;

        return $this;
    }

    public function getM7(): ?int
    {
        return $this->m7;
    }

    public function setM7(?int $m7): self
    {
        $this->m7 = $m7;

        return $this;
    }

    public function getM8(): ?int
    {
        return $this->m8;
    }

    public function setM8(?int $m8): self
    {
        $this->m8 = $m8;

        return $this;
    }

    public function getM9(): ?int
    {
        return $this->m9;
    }

    public function setM9(?int $m9): self
    {
        $this->m9 = $m9;

        return $this;
    }

    public function getM10(): ?int
    {
        return $this->m10;
    }

    public function setM10(?int $m10): self
    {
        $this->m10 = $m10;

        return $this;
    }

    public function getM11(): ?int
    {
        return $this->m11;
    }

    public function setM11(?int $m11): self
    {
        $this->m11 = $m11;

        return $this;
    }

    public function getM12(): ?int
    {
        return $this->m12;
    }

    public function setM12(?int $m12): self
    {
        $this->m12 = $m12;

        return $this;
    }

    public function getChapter(): ?Chapter
    {
        return $this->chapter;
    }

    public function setChapter(Chapter $chapter): self
    {
        $this->chapter = $chapter;

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
