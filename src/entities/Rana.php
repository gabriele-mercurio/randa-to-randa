<?php

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="rana")
 */
class Rana
{
    /** 
     * @ORM\Id
     * @ORM\Column(type="integer") */
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

}
