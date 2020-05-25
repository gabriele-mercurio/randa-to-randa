<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ChapterRepository")
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
    // PROJECT | CORE_GROUP | CHAPTER
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

    /** @ORM\Column(type="integer") */
    private $members;

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

    /** Get the value of members */
    public function getMembers(): int
    {
        return (int) $this->members;
    }

    /** Set the value of members */
    public function setMembers(int $members): self
    {
        $this->members = $members;
        return $this;
    }

    /** Get the value of prevLaunchCoregroupDate */
    public function getPrevLaunchCoregroupDate(): DateTime
    {
        return $this->prevLaunchCoregroupDate;
    }

    /** Set the value of prevLaunchCoregroupDate */
    public function setPrevLaunchCoregroupDate(DateTime $prevLaunchCoregroupDate): self
    {
        $this->prevLaunchCoregroupDate = $prevLaunchCoregroupDate;
        return $this;
    }

    /** Get the value of actualLaunchCoregroupDate */
    public function getActualLaunchCoregroupDate(): DateTime
    {
        return $this->actualLaunchCoregroupDate;
    }

    /** Set the value of actualLaunchCoregroupDate */
    public function setActualLaunchCoregroupDate(DateTime $actualLaunchCoregroupDate): self
    {
        $this->actualLaunchCoregroupDate = $actualLaunchCoregroupDate;
        return $this;
    }

    /** Get the value of prevLaunchChatperDate */
    public function getPrevLaunchChatperDate(): DateTime
    {
        return $this->prevLaunchChatperDate;
    }

    /** Set the value of prevLaunchChatperDate */
    public function setPrevLaunchChatperDate(DateTime $prevLaunchChatperDate): self
    {
        $this->prevLaunchChatperDate = $prevLaunchChatperDate;
        return $this;
    }

    /** Get the value of actualLaunchChatperDate */
    public function getActualLaunchChatperDate(): DateTime
    {
        return $this->actualLaunchChatperDate;
    }

    /** Set the value of actualLaunchChatperDate */
    public function setActualLaunchChatperDate(DateTime $actualLaunchChatperDate): self
    {
        $this->actualLaunchChatperDate = $actualLaunchChatperDate;
        return $this;
    }

    /** Get the value of suspDate */
    public function getSuspDate(): DateTime
    {
        return $this->suspDate;
    }

    /** Set the value of suspDate */
    public function setSuspDate(DateTime $suspDate): self
    {
        $this->suspDate = $suspDate;
        return $this;
    }

    /** Get the value of prevResumeDate */
    public function getPrevResumeDate(): DateTime
    {
        return $this->prevResumeDate;
    }

    /** Set the value of prevResumeDate */
    public function setPrevResumeDate(DateTime $prevResumeDate): self
    {
        $this->prevResumeDate = $prevResumeDate;
        return $this;
    }

    /** Get the value of actualResumeDate */
    public function getActualResumeDate(): DateTime
    {
        return $this->actualResumeDate;
    }

    /** Set the value of actualResumeDate */
    public function setActualResumeDate(DateTime $actualResumeDate): self
    {
        $this->actualResumeDate = $actualResumeDate;
        return $this;
    }

    /** Get the value of closureDate */
    public function getClosureDate(): DateTime
    {
        return $this->closureDate;
    }

    /** Set the value of closureDate */
    public function setClosureDate(DateTime $closureDate): self
    {
        $this->closureDate = $closureDate;
        return $this;
    }
}
