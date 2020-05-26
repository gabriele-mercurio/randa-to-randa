<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="chapters")
 */
class Chapter
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /** @ORM\Column(type="string", length=32) */
    private $name;

    /** @ORM\Column(name="current_state", type="string", length=10, options={"default":"PROJECT"}) */
    private $currentState;

    /**
     * @ORM\ManyToOne(targetEntity="Region", cascade={"all"}, fetch="LAZY")
     * @ORM\JoinColumn(nullable=false)
     */
    private $region;

    /**
     * @ORM\ManyToOne(targetEntity="Director", cascade={"all"}, fetch="LAZY")
     * @ORM\JoinColumn(nullable=false)
     */
    private $director;

    /** @ORM\Column(name="prev_launch_coregroup_date", type="date") */
    private $prevLaunchCoregroupDate;

    /** @ORM\Column(name="actual_launch_coregroup_date", type="date") */
    private $actualLaunchCoregroupDate;

    /** @ORM\Column(name="prev_launch_chatper_date", type="date") */
    private $prevLaunchChatperDate;

    /** @ORM\Column(name="actual_launch_chatper_date", type="date") */
    private $actualLaunchChatperDate;

    /** @ORM\Column(name="susp_date", type="date") */
    private $suspDate;

    /** @ORM\Column(name="prev_resume_date", type="date") */
    private $prevResumeDate;

    /** @ORM\Column(name="actual_resume_date", type="date") */
    private $actualResumeDate;

    /** @ORM\Column(name="closure_date", type="date") */
    private $closureDate;

    /** Get the value of id */
    public function getId()
    {
        return $this->id;
    }

    /** Get the value of name */
    public function getName()
    {
        return $this->name;
    }

    /** Set the value of name */
    public function setName($name): self
    {
        $this->name = $name;
        return $this;
    }

    /** Get the value of currentState */
    public function getCurrentState()
    {
        return $this->currentState;
    }

    /** Set the value of currentState */
    public function setCurrentState($currentState): self
    {
        $this->currentState = $currentState;
        return $this;
    }

    /** Get the value of region */
    public function getRegion()
    {
        return $this->region;
    }

    /** Set the value of region */
    public function setRegion($region): self
    {
        $this->region = $region;
        return $this;
    }

    /** Get the value of director */
    public function getDirector()
    {
        return $this->director;
    }

    /** Set the value of director */
    public function setDirector($director): self
    {
        $this->director = $director;
        return $this;
    }

    /** Get the value of prevLaunchCoregroupDate */
    public function getPrevLaunchCoregroupDate()
    {
        return $this->prevLaunchCoregroupDate;
    }

    /** Set the value of prevLaunchCoregroupDate */
    public function setPrevLaunchCoregroupDate($prevLaunchCoregroupDate): self
    {
        $this->prevLaunchCoregroupDate = $prevLaunchCoregroupDate;
        return $this;
    }

    /** Get the value of actualLaunchCoregroupDate */
    public function getActualLaunchCoregroupDate()
    {
        return $this->actualLaunchCoregroupDate;
    }

    /** Set the value of actualLaunchCoregroupDate */
    public function setActualLaunchCoregroupDate($actualLaunchCoregroupDate): self
    {
        $this->actualLaunchCoregroupDate = $actualLaunchCoregroupDate;
        return $this;
    }

    /** Get the value of prevLaunchChatperDate */
    public function getPrevLaunchChatperDate()
    {
        return $this->prevLaunchChatperDate;
    }

    /** Set the value of prevLaunchChatperDate */
    public function setPrevLaunchChatperDate($prevLaunchChatperDate): self
    {
        $this->prevLaunchChatperDate = $prevLaunchChatperDate;
        return $this;
    }

    /** Get the value of actualLaunchChatperDate */
    public function getActualLaunchChatperDate()
    {
        return $this->actualLaunchChatperDate;
    }

    /** Set the value of actualLaunchChatperDate */
    public function setActualLaunchChatperDate($actualLaunchChatperDate): self
    {
        $this->actualLaunchChatperDate = $actualLaunchChatperDate;
        return $this;
    }

    /** Get the value of suspDate */
    public function getSuspDate()
    {
        return $this->suspDate;
    }

    /** Set the value of suspDate */
    public function setSuspDate($suspDate): self
    {
        $this->suspDate = $suspDate;
        return $this;
    }

    /** Get the value of prevResumeDate */
    public function getPrevResumeDate()
    {
        return $this->prevResumeDate;
    }

    /** Set the value of prevResumeDate */
    public function setPrevResumeDate($prevResumeDate): self
    {
        $this->prevResumeDate = $prevResumeDate;
        return $this;
    }

    /** Get the value of actualResumeDate */
    public function getActualResumeDate()
    {
        return $this->actualResumeDate;
    }

    /** Set the value of actualResumeDate */
    public function setActualResumeDate($actualResumeDate): self
    {
        $this->actualResumeDate = $actualResumeDate;
        return $this;
    }

    /** Get the value of closureDate */
    public function getClosureDate()
    {
        return $this->closureDate;
    }

    /** Set the value of closureDate */
    public function setClosureDate($closureDate): self
    {
        $this->closureDate = $closureDate;
        return $this;
    }
}
