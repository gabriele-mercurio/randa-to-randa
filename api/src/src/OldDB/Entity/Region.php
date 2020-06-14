<?php

namespace App\OldDB\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\OldDB\Repository\RegionRepository")
 * @ORM\Table(name="Region2")
 */
class Region
{
    /**
     * @ORM\Id()
     * @ORM\Column(name="ID", type="integer", unique=true)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /** @ORM\Column(type="string", length=45) */
    private $nome;

    /** @ORM\Column(name="T2", type="string", length=45) */
    private $T2;

    /** @ORM\Column(name="T3", type="string", length=45) */
    private $T3;

    /** @ORM\Column(name="T4", type="string", length=45) */
    private $T4;

    /** @ORM\Column(type="string", length=45) */
    private $randa;

    /** @ORM\Column(name="annoP", type="integer") */
    private $annoP;

    /** @ORM\Column(name="annoC", type="integer") */
    private $annoC;

    /** @ORM\Column(name="annoS", type="integer") */
    private $annoS;

    /** @ORM\Column(name="annoOggi", type="integer") */
    private $annoOggi;

    /** @ORM\Column(name="meseOggi", type="integer") */
    private $meseOggi;

    /** @ORM\Column(name="DirT1", type="integer") */
    private $dirT1;

    /** @ORM\Column(name="DirT2", type="integer") */
    private $dirT2;

    /** @ORM\Column(name="DirT3", type="integer") */
    private $dirT3;

    /** @ORM\Column(name="DirT4", type="integer") */
    private $dirT4;

    /** @ORM\Column(name="NotaP", type="text") */
    private $notaP;

    /** @ORM\Column(name="NotaO", type="text") */
    private $notaO;

    /** @ORM\Column(name="NotaS", type="text") */
    private $notaS;

    /** @ORM\Column(name="NotaReso", type="text") */
    private $notaReso;

    /**
     * @var Collection|Director[]
     *
     * @ORM\OneToMany(targetEntity="Director", mappedBy="region")
     */
    private $directors;

    /** Get the value of id */
    public function getId(): int
    {
        return $this->id;
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

    /** Get the value of T2 */
    public function getT2(): string
    {
        return $this->T2;
    }

    /** Set the value of T2 */
    public function setT2(string $T2): self
    {
        $this->T2 = $T2;
        return $this;
    }

    /** Get the value of T3 */
    public function getT3(): string
    {
        return $this->T3;
    }

    /** Set the value of Compenso */
    public function setT3(string $T3): self
    {
        $this->T3 = $T3;
        return $this;
    }

    /** Get the value of T4 */
    public function getT4(): string
    {
        return $this->T4;
    }

    /** Set the value of T4 */
    public function setT4(string $T4): self
    {
        $this->T4 = $T4;
        return $this;
    }

    /** Get the value of randa */
    public function getRanda(): string
    {
        return $this->randa;
    }

    /** Set the value of randa */
    public function setRanda(string $randa): self
    {
        $this->randa = $randa;
        return $this;
    }

    /** Get the value of annoP */
    public function getAnnoP(): int
    {
        return $this->annoP;
    }

    /** Set the value of annoP */
    public function setAnnoP(int $annoP): self
    {
        $this->annoP = $annoP;
        return $this;
    }

    /** Get the value of annoC */
    public function getAnnoC(): int
    {
        return $this->annoC;
    }

    /** Set the value of annoC */
    public function setAnnoC(int $annoC): self
    {
        $this->annoC = $annoC;
        return $this;
    }

    /** Get the value of annoS */
    public function getAnnoS(): int
    {
        return $this->annoS;
    }

    /** Set the value of annoS */
    public function setAnnoS(int $annoS): self
    {
        $this->annoS = $annoS;
        return $this;
    }

    /** Get the value of annoOggi */
    public function getAnnoOggi(): int
    {
        return $this->annoOggi;
    }

    /** Set the value of annoOggi */
    public function setAnnoOggi(int $annoOggi): self
    {
        $this->annoOggi = $annoOggi;
        return $this;
    }

    /** Get the value of meseOggi */
    public function getMeseOggi(): int
    {
        return $this->meseOggi;
    }

    /** Set the value of meseOggi */
    public function setMeseOggi(int $meseOggi): self
    {
        $this->meseOggi = $meseOggi;
        return $this;
    }

    /** Get the value of dirT1 */
    public function getDirT1(): int
    {
        return $this->dirT1;
    }

    /** Set the value of dirT1 */
    public function setDirT1(int $dirT1): self
    {
        $this->dirT1 = $dirT1;
        return $this;
    }

    /** Get the value of dirT2 */
    public function getDirT2(): int
    {
        return $this->dirT2;
    }

    /** Set the value of dirT2 */
    public function setDirT2(int $dirT2): self
    {
        $this->dirT2 = $dirT2;
        return $this;
    }

    /** Get the value of dirT3 */
    public function getDirT3(): int
    {
        return $this->dirT3;
    }

    /** Set the value of dirT3 */
    public function setDirT3(int $dirT3): self
    {
        $this->dirT3 = $dirT3;
        return $this;
    }

    /** Get the value of dirT4 */
    public function getDirT4(): int
    {
        return $this->dirT4;
    }

    /** Set the value of dirT4 */
    public function setDirT4(int $dirT4): self
    {
        $this->dirT4 = $dirT4;
        return $this;
    }

    /** Get the value of notaP */
    public function getNotaP(): string
    {
        return $this->notaP;
    }

    /** Set the value of notaP */
    public function setNotaP(string $notaP): self
    {
        $this->notaP = $notaP;
        return $this;
    }

    /** Get the value of notaO */
    public function getNotaO(): string
    {
        return $this->notaO;
    }

    /** Set the value of notaO */
    public function setNotaO(string $notaO): self
    {
        $this->notaO = $notaO;
        return $this;
    }

    /** Get the value of notaS */
    public function getNotaS(): string
    {
        return $this->notaS;
    }

    /** Set the value of notaS */
    public function setNotaS(string $notaS): self
    {
        $this->notaS = $notaS;
        return $this;
    }

    /** Get the value of notaReso */
    public function getNotaReso(): string
    {
        return $this->notaReso;
    }

    /** Set the value of notaReso */
    public function setNotaReso(string $notaReso): self
    {
        $this->notaReso = $notaReso;
        return $this;
    }

    /**
     * @return Collection|Director[]
     */
    public function getDirectors()
    {
        return $this->directors;
    }
}
