<?php

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
     * @ORM\GeneratedValue(strategy="IDENTITY") */     
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /** @ORM\Column(type="string", length=10) */
    //EXECUTIVE | AREA | ASSISTANT
    private $role;

    /**
    * @ORM\ManyToOne(targetEntity="Director", fetch="LAZY")
    */
    private $supervisor;

    /** @ORM\Column(type="string", length=8) */
    //MONTHLY | ANNUAL
    private $pay_type;

    /** @ORM\Column(type="float", options={"default":0}) */
    private $launch_percentage;

    /** @ORM\Column(type="float", options={"default":0}) */
    private $green_light_percentage;

    /** @ORM\Column(type="float", options={"default":0}) */
    private $yellow_light_percentage;

    /** @ORM\Column(type="float", options={"default":0}) */
    private $red_light_percentage;

    /** @ORM\Column(type="float", options={"default":0}) */
    private $grey_light_percentage;

    /** @ORM\Column(type="float", options={"default":0}) */
    private $fixed_percentage;



    /**
     * Get the value of id
     */ 
    public function getId()
    {
        return $this->id;
    }


    /**
     * Get the value of user
     */ 
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set the value of user
     *
     * @return  self
     */ 
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get the value of role
     */ 
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Set the value of role
     *
     * @return  self
     */ 
    public function setRole($role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Get the value of supervisor
     */ 
    public function getSupervisor()
    {
        return $this->supervisor;
    }

    /**
     * Set the value of supervisor
     *
     * @return  self
     */ 
    public function setSupervisor($supervisor)
    {
        $this->supervisor = $supervisor;

        return $this;
    }

    /**
     * Get the value of pay_type
     */ 
    public function getPay_type()
    {
        return $this->pay_type;
    }

    /**
     * Set the value of pay_type
     *
     * @return  self
     */ 
    public function setPay_type($pay_type)
    {
        $this->pay_type = $pay_type;

        return $this;
    }

    /**
     * Get the value of launch_percentage
     */ 
    public function getLaunch_percentage()
    {
        return $this->launch_percentage;
    }

    /**
     * Set the value of launch_percentage
     *
     * @return  self
     */ 
    public function setLaunch_percentage($launch_percentage)
    {
        $this->launch_percentage = $launch_percentage;

        return $this;
    }

    /**
     * Get the value of green_light_percentage
     */ 
    public function getGreen_light_percentage()
    {
        return $this->green_light_percentage;
    }

    /**
     * Set the value of green_light_percentage
     *
     * @return  self
     */ 
    public function setGreen_light_percentage($green_light_percentage)
    {
        $this->green_light_percentage = $green_light_percentage;

        return $this;
    }

    /**
     * Get the value of yellow_light_percentage
     */ 
    public function getYellow_light_percentage()
    {
        return $this->yellow_light_percentage;
    }

    /**
     * Set the value of yellow_light_percentage
     *
     * @return  self
     */ 
    public function setYellow_light_percentage($yellow_light_percentage)
    {
        $this->yellow_light_percentage = $yellow_light_percentage;

        return $this;
    }

    /**
     * Get the value of red_light_percentage
     */ 
    public function getRed_light_percentage()
    {
        return $this->red_light_percentage;
    }

    /**
     * Set the value of red_light_percentage
     *
     * @return  self
     */ 
    public function setRed_light_percentage($red_light_percentage)
    {
        $this->red_light_percentage = $red_light_percentage;

        return $this;
    }

    /**
     * Get the value of grey_light_percentage
     */ 
    public function getGrey_light_percentage()
    {
        return $this->grey_light_percentage;
    }

    /**
     * Set the value of grey_light_percentage
     *
     * @return  self
     */ 
    public function setGrey_light_percentage($grey_light_percentage)
    {
        $this->grey_light_percentage = $grey_light_percentage;

        return $this;
    }

    /**
     * Get the value of fixed_percentage
     */ 
    public function getFixed_percentage()
    {
        return $this->fixed_percentage;
    }

    /**
     * Set the value of fixed_percentage
     *
     * @return  self
     */ 
    public function setFixed_percentage($fixed_percentage)
    {
        $this->fixed_percentage = $fixed_percentage;

        return $this;
    }
}
