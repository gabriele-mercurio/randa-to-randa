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
     * @ORM\Column(type="integer") */
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

}
