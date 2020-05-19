<?php

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="regions")
 */
class Region
{
    /** 
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY") */
    private $id;

    /** @ORM\Column(type="string", length=32) */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity="Director", cascade={"all"}, fetch="LAZY")
     * @ORM\JoinColumn(nullable=false)
     */
    private $executive_director;

    /** @ORM\Column(type="text", nullable=true) */
    private $notes;


    /**
     * Get the value of id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get the value of name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the value of name
     *
     * @return  self
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get the value of executive_director
     */
    public function getExecutive_director()
    {
        return $this->executive_director;
    }

    /**
     * Set the value of executive_director
     *
     * @return  self
     */
    public function setExecutive_director($executive_director)
    {
        $this->executive_director = $executive_director;

        return $this;
    }

    /**
     * Get the value of notes
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * Set the value of notes
     *
     * @return  self
     */
    public function setNotes($notes)
    {
        $this->notes = $notes;

        return $this;
    }
}
