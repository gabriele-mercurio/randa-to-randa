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
     * @ORM\Column(type="integer") */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="User")
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


}
