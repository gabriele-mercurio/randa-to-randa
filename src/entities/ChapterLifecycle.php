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
     * @ORM\Column(type="integer") */
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

}
