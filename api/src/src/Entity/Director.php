<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="directors")
 */
class Director
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /** @ORM\Column(type="string", length=10) */
    //EXECUTIVE | AREA | ASSISTANT
    private $role;

    /** @ORM\ManyToOne(targetEntity="Region") */
    private $region;

    /** @ORM\ManyToOne(targetEntity="Director", fetch="LAZY") */
    private $supervisor;

    /** @ORM\Column(name="pay_type", type="string", length=8) */
    //MONTHLY | ANNUAL
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

    /** Get the value of id */
    public function getId()
    {
        return $this->id;
    }

    /** Get the value of user */
    public function getUser()
    {
        return $this->user;
    }

    /** Set the value of user */
    public function setUser($user): self
    {
        $this->user = $user;
        return $this;
    }

    /** Get the value of role */
    public function getRole()
    {
        return $this->role;
    }

    /** Set the value of role */
    public function setRole($role): self
    {
        $this->role = $role;
        return $this;
    }

    /** Get the value of supervisor */
    public function getSupervisor()
    {
        return $this->supervisor;
    }

    /** Set the value of supervisor */
    public function setSupervisor($supervisor): self
    {
        $this->supervisor = $supervisor;
        return $this;
    }

    /** Get the value of payType */
    public function getPay_type()
    {
        return $this->payType;
    }

    /** Set the value of payType */
    public function setPay_type($payType): self
    {
        $this->payType = $payType;
        return $this;
    }

    /** Get the value of launchPercentage */
    public function getLaunchPercentage()
    {
        return $this->launchPercentage;
    }

    /** Set the value of launchPercentage */
    public function setLaunchPercentage($launchPercentage): self
    {
        $this->launchPercentage = $launchPercentage;
        return $this;
    }

    /** Get the value of greenLightPercentage */
    public function getGreenLightPercentage()
    {
        return $this->greenLightPercentage;
    }

    /** Set the value of greenLightPercentage */
    public function setGreenLightPercentage($greenLightPercentage): self
    {
        $this->greenLightPercentage = $greenLightPercentage;
        return $this;
    }

    /** Get the value of yellowLightPercentage */
    public function getYellowLightPercentage()
    {
        return $this->yellowLightPercentage;
    }

    /** Set the value of yellowLightPercentage */
    public function setYellowLightPercentage($yellowLightPercentage): self
    {
        $this->yellowLightPercentage = $yellowLightPercentage;
        return $this;
    }

    /** Get the value of redLightPercentage */
    public function getRedLightPercentage()
    {
        return $this->redLightPercentage;
    }

    /** Set the value of redLightPercentage */
    public function setRedLightPercentage($redLightPercentage): self
    {
        $this->redLightPercentage = $redLightPercentage;
        return $this;
    }

    /** Get the value of greyLightPercentage */
    public function getGrey_light_percentage()
    {
        return $this->greyLightPercentage;
    }

    /** Set the value of greyLightPercentage */
    public function setGrey_light_percentage($greyLightPercentage): self
    {
        $this->greyLightPercentage = $greyLightPercentage;
        return $this;
    }

    /** Get the value of fixedPercentage */
    public function getFixedPercentage()
    {
        return $this->fixedPercentage;
    }

    /** Set the value of fixedPercentage */
    public function setFixedPercentage($fixedPercentage): self
    {
        $this->fixedPercentage = $fixedPercentage;
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
}
