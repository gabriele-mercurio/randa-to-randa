<?php

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="month_ly_consumptive")
 */
class month_lyConsumptive
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

    /** 
     * @ORM\Column(type="integer", nullable=true)
     */
    private $month_1;

    /** 
     * @ORM\Column(type="integer", nullable=true)
     */
    private $month_2;

    /** 
     * @ORM\Column(type="integer", nullable=true)
     */
    private $month_3;

    /** 
     * @ORM\Column(type="integer", nullable=true)
     */
    private $month_4;

    /** 
     * @ORM\Column(type="integer", nullable=true)
     */
    private $month_5;

    /** 
     * @ORM\Column(type="integer", nullable=true)
     */
    private $month_6;

    /** 
     * @ORM\Column(type="integer", nullable=true)
     */
    private $month_7;

    /** 
     * @ORM\Column(type="integer", nullable=true)
     */
    private $month_8;

    /** 
     * @ORM\Column(type="integer", nullable=true)
     */
    private $month_9;

    /** 
     * @ORM\Column(type="integer", nullable=true)
     */
    private $month_10;

    /** 
     * @ORM\Column(type="integer", nullable=true)
     */
    private $month_11;

    /** 
     * @ORM\Column(type="integer", nullable=true)
     */
    private $month_12;





}
