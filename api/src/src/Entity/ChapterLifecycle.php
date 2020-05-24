<?php

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="chapter_lifecycle")
 */
class ChapterLifecycle
{
    /** 
     * @ORM\Id
     * @ORM\Column(type="integer") 
     * @ORM\GeneratedValue(strategy="IDENTITY") */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="Chapter")
     * @ORM\JoinColumn(nullable=false)
     */
    private $chapter;

    /** 
     * @ORM\Column(type="date") */
    private $prev_launch_coregroup_date;

    /** 
     * @ORM\Column(type="date") */
    private $actual_launch_coregroup_date;

    /** 
     * @ORM\Column(type="date") */
    private $prev_launch_chatper_date;

    /** 
     * @ORM\Column(type="date") */
    private $actual_launch_chatper_date;

    /** 
     * @ORM\Column(type="date") */
    private $susp_date;

    /** 
     * @ORM\Column(type="date") */
    private $prev_resume_date;

    /** 
     * @ORM\Column(type="date") */
    private $actual_resume_date;

    /** 
     * @ORM\Column(type="date") */
    private $closure_date;


    /**
     * Get the value of id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get the value of chapter
     */
    public function getChapter()
    {
        return $this->chapter;
    }

    /**
     * Set the value of chapter
     *
     * @return  self
     */
    public function setChapter($chapter)
    {
        $this->chapter = $chapter;

        return $this;
    }

    /**
     * Get the value of prev_launch_coregroup_date
     */
    public function getPrev_launch_coregroup_date()
    {
        return $this->prev_launch_coregroup_date;
    }

    /**
     * Set the value of prev_launch_coregroup_date
     *
     * @return  self
     */
    public function setPrev_launch_coregroup_date($prev_launch_coregroup_date)
    {
        $this->prev_launch_coregroup_date = $prev_launch_coregroup_date;

        return $this;
    }

    /**
     * Get the value of actual_launch_coregroup_date
     */
    public function getActual_launch_coregroup_date()
    {
        return $this->actual_launch_coregroup_date;
    }

    /**
     * Set the value of actual_launch_coregroup_date
     *
     * @return  self
     */
    public function setActual_launch_coregroup_date($actual_launch_coregroup_date)
    {
        $this->actual_launch_coregroup_date = $actual_launch_coregroup_date;

        return $this;
    }

    /**
     * Get the value of prev_launch_chatper_date
     */
    public function getPrev_launch_chatper_date()
    {
        return $this->prev_launch_chatper_date;
    }

    /**
     * Set the value of prev_launch_chatper_date
     *
     * @return  self
     */
    public function setPrev_launch_chatper_date($prev_launch_chatper_date)
    {
        $this->prev_launch_chatper_date = $prev_launch_chatper_date;

        return $this;
    }

    /**
     * Get the value of actual_launch_chatper_date
     */
    public function getActual_launch_chatper_date()
    {
        return $this->actual_launch_chatper_date;
    }

    /**
     * Set the value of actual_launch_chatper_date
     *
     * @return  self
     */
    public function setActual_launch_chatper_date($actual_launch_chatper_date)
    {
        $this->actual_launch_chatper_date = $actual_launch_chatper_date;

        return $this;
    }

    /**
     * Get the value of susp_date
     */
    public function getSusp_date()
    {
        return $this->susp_date;
    }

    /**
     * Set the value of susp_date
     *
     * @return  self
     */
    public function setSusp_date($susp_date)
    {
        $this->susp_date = $susp_date;

        return $this;
    }

    /**
     * Get the value of prev_resume_date
     */
    public function getPrev_resume_date()
    {
        return $this->prev_resume_date;
    }

    /**
     * Set the value of prev_resume_date
     *
     * @return  self
     */
    public function setPrev_resume_date($prev_resume_date)
    {
        $this->prev_resume_date = $prev_resume_date;

        return $this;
    }

    /**
     * Get the value of actual_resume_date
     */
    public function getActual_resume_date()
    {
        return $this->actual_resume_date;
    }

    /**
     * Set the value of actual_resume_date
     *
     * @return  self
     */
    public function setActual_resume_date($actual_resume_date)
    {
        $this->actual_resume_date = $actual_resume_date;

        return $this;
    }

    /**
     * Get the value of closure_date
     */
    public function getClosure_date()
    {
        return $this->closure_date;
    }

    /**
     * Set the value of closure_date
     *
     * @return  self
     */
    public function setClosure_date($closure_date)
    {
        $this->closure_date = $closure_date;

        return $this;
    }
}
