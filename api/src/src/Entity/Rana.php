<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="rana")
 */
class Rana
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="Chapter", cascade={"all"}, fetch="LAZY")
     * @ORM\JoinColumn(nullable=false)
     */
    private $chapter;

    /**
     * @ORM\ManyToOne(targetEntity="Randa", cascade={"all"}, fetch="LAZY")
     * @ORM\JoinColumn(nullable=false)
     */
    private $randa;

    /** Get the value of id */
    public function getId()
    {
        return $this->id;
    }

    /** Get the value of chapter */
    public function getChapter()
    {
        return $this->chapter;
    }

    /** Set the value of chapter */
    public function setChapter($chapter): self
    {
        $this->chapter = $chapter;
        return $this;
    }

    /** Get the value of randa */
    public function getRanda()
    {
        return $this->randa;
    }

    /** Set the value of randa */
    public function setRanda($randa): self
    {
        $this->randa = $randa;
        return $this;
    }
}
