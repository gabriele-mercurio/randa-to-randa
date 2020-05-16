<?php

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="chapters")
 */
class Chapter
{
    /** 
     * @ORM\Id
     * @ORM\Column(type="integer") */
    private $id;

    /** @ORM\Column(type="string", length=32) */
    private $name;

    /** @ORM\Column(type="string", length=10, options={"default":"PROJECT"}) */
    private $current_state;

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

}
