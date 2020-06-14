<?php

namespace App\OldDB\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\OldDB\Entity\Region;
use App\OldDB\Repository\RegionRepository;

/**
 * @ORM\Entity(repositoryClass="App\OldDB\Repository\DirectorRepository")
 * @ORM\Table(name="Director")
 */
class Director
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="integer", unique=true)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /** @ORM\Column(name="id_Region", type="integer") */
    private $regionId;

    /** @ORM\Column(name="Nome", type="text") */
    private $nome;

    /** @ORM\Column(name="Email", type="text") */
    private $email;

    /** @ORM\Column(name="Password", type="text") */
    private $password;

    /** @ORM\Column(name="Level", type="smallint") */
    private $level;

    /** @ORM\Column(name="Compenso", type="text") */
    private $compenso;

    /** @ORM\Column(name="Lancio", type="float", options={"default":0}) */
    private $lancio;

    /** @ORM\Column(name="GreenLight", type="float", options={"default":0}) */
    private $greenLight;

    /** @ORM\Column(name="YellowLight", type="float", options={"default":0}) */
    private $yellowLight;

    /** @ORM\Column(name="RedLight", type="float", options={"default":0}) */
    private $redLight;

    /** @ORM\Column(name="GreyLight", type="float", options={"default":0}) */
    private $greyLight;

    /** @ORM\Column(name="CompArea", type="float", options={"default":0}) */
    private $compArea;

    /** @ORM\Column(name="Area", type="integer") */
    private $area;

    /** @ORM\Column(name="chkPw", type="integer") */
    private $chkPw;

    /** @ORM\Column(name="CompFisso", type="float", options={"default":0}) */
    private $compFisso;

    /** Get the value of id */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Get the value of region
     *
     * @return Region|null
     */
    public function getRegion(RegionRepository $regionRepository): ?Region
    {
        return $regionRepository->find($this->regionId);
    }

    /** Set the value of region */
    public function setRegion(Region $region): self
    {
        $this->regionId = $region->getId();
        return $this;
    }

    /** Get the value of nome */
    public function getNome(): string
    {
        return $this->nome;
    }

    /** Set the value of nome */
    public function setNome(string $nome): self
    {
        $this->nome = $nome;
        return $this;
    }

    /** Get the value of email */
    public function getEmail(): string
    {
        return $this->email;
    }

    /** Set the value of email */
    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    /** Get the value of password */
    public function getPassword(): string
    {
        return $this->password;
    }

    /** Set the value of password */
    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    /** Get the value of level */
    public function getLevel(): int
    {
        return $this->level;
    }

    /** Set the value of level */
    public function setLevel(int $level): self
    {
        $this->level = $level;
        return $this;
    }

    /** Get the value of compenso */
    public function getCompenso(): string
    {
        return $this->compenso;
    }

    /** Set the value of compenso */
    public function setCompenso(string $compenso): self
    {
        $this->compenso = $compenso;
        return $this;
    }

    /** Get the value of lancio */
    public function getLancio(): float
    {
        return $this->lancio;
    }

    /** Set the value of lancio */
    public function setLancio(float $lancio): self
    {
        $this->lancio = $lancio;
        return $this;
    }

    /** Get the value of greenLight */
    public function getGreenLight(): float
    {
        return $this->greenLight;
    }

    /** Set the value of greenLight */
    public function setGreenLight(float $greenLight): self
    {
        $this->greenLight = $greenLight;
        return $this;
    }

    /** Get the value of yellowLight */
    public function getYellowLight(): float
    {
        return $this->yellowLight;
    }

    /** Set the value of yellowLight */
    public function setYellowLight(float $yellowLight): self
    {
        $this->yellowLight = $yellowLight;
        return $this;
    }

    /** Get the value of redLight */
    public function getRedLight(): float
    {
        return $this->redLight;
    }

    /** Set the value of redLight */
    public function setRedLight(float $redLight): self
    {
        $this->redLight = $redLight;
        return $this;
    }

    /** Get the value of greyLight */
    public function getGreyLight(): float
    {
        return $this->greyLight;
    }

    /** Set the value of greyLight */
    public function setGreyLight(float $greyLight): self
    {
        $this->greyLight = $greyLight;
        return $this;
    }

    /** Get the value of compArea */
    public function getCompArea(): float
    {
        return $this->compArea;
    }

    /** Set the value of compArea */
    public function setCompArea(float $compArea): self
    {
        $this->compArea = $compArea;
        return $this;
    }

    /** Get the value of area */
    public function getArea(): int
    {
        return $this->area;
    }

    /** Set the value of area */
    public function setArea(int $area): self
    {
        $this->area = $area;
        return $this;
    }

    /** Get the value of chkPw */
    public function getChkPw(): int
    {
        return $this->chkPw;
    }

    /** Set the value of chkPw */
    public function setChkPw(int $chkPw): self
    {
        $this->chkPw = $chkPw;
        return $this;
    }

    /** Get the value of compFisso */
    public function getCompFisso(): float
    {
        return $this->compFisso;
    }

    /** Set the value of compFisso */
    public function setCompFisso(float $compFisso): self
    {
        $this->compFisso = $compFisso;
        return $this;
    }
}
