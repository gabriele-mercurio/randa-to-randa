<?php

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="leaving_members")
 */
class LeavingMembers
{
    /** 
     * @ORM\Id
     * @ORM\Column(type="integer") */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Rana", cascade={"all"}, fetch="LAZY")
     * @ORM\JoinColumn(nullable=false)
     */
    private $rana;

    /** @ORM\Column(type="string", length=2) */
    //T0 | T1 | T2 | T3 | T4
    private $time_slot;

    /** @ORM\Column(type="integer", nullable=true) */
    private $prev_t1;

    /** @ORM\Column(type="integer", nullable=true) */
    private $prev_t2;

    /** @ORM\Column(type="integer", nullable=true) */
    private $prev_t3;

    /** @ORM\Column(type="integer", nullable=true) */
    private $prev_t4;

    /** @ORM\Column(type="integer", nullable=true) */
    private $rev_t1;

    /** @ORM\Column(type="integer", nullable=true) */
    private $rev_t2;

    /** @ORM\Column(type="integer", nullable=true) */
    private $rev_t3;

    /** @ORM\Column(type="integer", nullable=true) */
    private $rev_t4;

    /** @ORM\Column(type="integer", nullable=true) */
    private $cons_t1;

    /** @ORM\Column(type="integer", nullable=true) */
    private $cons_t2;

    /** @ORM\Column(type="integer", nullable=true) */
    private $cons_t3;

    /** @ORM\Column(type="integer", nullable=true) */
    private $cons_t4;

    /** @ORM\Column(type="string", length=8, options={"default"="TODO"}) */
    // TODO | PROPOSED | APPROVED | REFUSED
    private $status;


}
