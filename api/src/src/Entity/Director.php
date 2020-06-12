<?php

namespace App\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DirectorRepository")
 * @ORM\Table(name="directors")
 */
class Director
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /** @ORM\Column(type="string", length=10) */
    private $role;

    /** @ORM\ManyToOne(targetEntity="Region") */
    private $region;

    /** @ORM\ManyToOne(targetEntity="Director", fetch="LAZY") */
    private $supervisor;

    /** @ORM\Column(name="free_account", type="boolean", nullable=false, options={"default":false}) */
    private $freeAccount;

    /** @ORM\Column(name="pay_type", type="string", length=8) */
    private $payType;

    /** @ORM\Column(name="launch_percentage", type="float", options={"default":0}) */
    private $launchPercentage;

    /** @ORM\Column(name="green_light_percentage", type="float", options={"default":0}) */
    private $greenLightPercentage;

    /** @ORM\Column(name="yellow_light_percentage", type="float", options={"default":0}) */
    private $yellowLightPercentage;

    /** @ORM\Column(name="red_light_percentage", type="float", options={"default":0}) */
    private $redLightPercentage;

    /** @ORM\Column(name="grey_light_percentage", type="float", options={"default":0}) */
    private $greyLightPercentage;

    /** @ORM\Column(name="fixed_percentage", type="float", options={"default":0}) */
    private $fixedPercentage;

    /** @ORM\Column(name="area_percentage", type="float", options={"default":0}) */
    private $areaPercentage;

    /**
     * @var Collection|Chapter[]
     *
     * @ORM\OneToMany(targetEntity="Chapter", mappedBy="director")
     */
    private $chapters;

    /**
     * @var Collection|Director[]
     *
     * @ORM\OneToMany(targetEntity="Director", mappedBy="supervisor")
     */
    private $subordinates;

    /**
     * Director constructor.
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

    /** Get the value of user */
    public function getUser(): User
    {
        return $this->user;
    }

    /** Set the value of user */
    public function setUser(User $user): self
    {
        $this->user = $user;
        return $this;
    }

    /** Get the value of role */
    public function getRole(): string
    {
        return $this->role;
    }

    /** Set the value of role */
    public function setRole(string $role): self
    {
        $this->role = $role;
        return $this;
    }

    /** Get the value of supervisor */
    public function getSupervisor(): ?Director
    {
        return $this->supervisor;
    }

    /** Set the value of supervisor */
    public function setSupervisor(?Director $supervisor): self
    {
        $this->supervisor = $supervisor;
        return $this;
    }

    /** Get the value of freeAccount */
    public function isFreeAccount(): bool
    {
        return !!$this->freeAccount;
    }

    /** Set the value of freeAccount */
    public function setFreeAccount(bool $freeAccount): self
    {
        $this->freeAccount = !!$freeAccount;
        return $this;
    }

    /** Get the value of payType */
    public function getPayType(): string
    {
        return $this->payType;
    }

    /** Set the value of payType */
    public function setPayType(string $payType): self
    {
        $this->payType = $payType;
        return $this;
    }

    /** Get the value of launchPercentage */
    public function getLaunchPercentage(): float
    {
        return $this->launchPercentage;
    }

    /** Set the value of launchPercentage */
    public function setLaunchPercentage(float $launchPercentage): self
    {
        $this->launchPercentage = $launchPercentage;
        return $this;
    }

    /** Get the value of greenLightPercentage */
    public function getGreenLightPercentage(): float
    {
        return $this->greenLightPercentage;
    }

    /** Set the value of greenLightPercentage */
    public function setGreenLightPercentage(float $greenLightPercentage): self
    {
        $this->greenLightPercentage = $greenLightPercentage;
        return $this;
    }

    /** Get the value of yellowLightPercentage */
    public function getYellowLightPercentage(): float
    {
        return $this->yellowLightPercentage;
    }

    /** Set the value of yellowLightPercentage */
    public function setYellowLightPercentage(float $yellowLightPercentage): self
    {
        $this->yellowLightPercentage = $yellowLightPercentage;
        return $this;
    }

    /** Get the value of redLightPercentage */
    public function getRedLightPercentage(): float
    {
        return $this->redLightPercentage;
    }

    /** Set the value of redLightPercentage */
    public function setRedLightPercentage(float $redLightPercentage): self
    {
        $this->redLightPercentage = $redLightPercentage;
        return $this;
    }

    /** Get the value of greyLightPercentage */
    public function getGreyLightPercentage(): float
    {
        return $this->greyLightPercentage;
    }

    /** Set the value of greyLightPercentage */
    public function setGreyLightPercentage(float $greyLightPercentage): self
    {
        $this->greyLightPercentage = $greyLightPercentage;
        return $this;
    }

    /** Get the value of fixedPercentage */
    public function getFixedPercentage(): float
    {
        return $this->fixedPercentage;
    }

    /** Set the value of fixedPercentage */
    public function setFixedPercentage(float $fixedPercentage): self
    {
        $this->fixedPercentage = $fixedPercentage;
        return $this;
    }

    /** Get the value of areaPercentage */
    public function getAreaPercentage(): float
    {
        return $this->areaPercentage;
    }

    /** Set the value of areaPercentage */
    public function setAreaPercentage(float $areaPercentage): self
    {
        $this->areaPercentage = $areaPercentage;
        return $this;
    }

    /** Get the value of region */
    public function getRegion(): Region
    {
        return $this->region;
    }

    /** Set the value of region */
    public function setRegion(Region $region): self
    {
        $this->region = $region;
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
    public function getSubordinates()
    {
        return $this->subordinates;
    }
}
