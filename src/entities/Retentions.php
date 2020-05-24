<?php

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="retentions")
 */
class Retentions
{
    /** 
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY") */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Rana", cascade={"all"}, fetch="LAZY")
     * @ORM\JoinColumn(nullable=false)
     */
    private $rana;

    /** @ORM\Column(type="string", length=4) */
    private $value_type;

    /** @ORM\Column(type="string", length=2) */
    // T0 | T1 | T2 | T3 | T4
    private $timeslot;

    /** @ORM\Column(type="integer", nullable=true) */
    private $m1;

    /** @ORM\Column(type="integer", nullable=true) */
    private $m2;

    /** @ORM\Column(type="integer", nullable=true) */
    private $m3;

    /** @ORM\Column(type="integer", nullable=true) */
    private $m4;

    /** @ORM\Column(type="integer", nullable=true) */
    private $m5;

    /** @ORM\Column(type="integer", nullable=true) */
    private $m6;

    /** @ORM\Column(type="integer", nullable=true) */
    private $m7;

    /** @ORM\Column(type="integer", nullable=true) */
    private $m8;

    /** @ORM\Column(type="integer", nullable=true) */
    private $m9;

    /** @ORM\Column(type="integer", nullable=true) */
    private $m10;

    /** @ORM\Column(type="integer", nullable=true) */
    private $m11;

    /** @ORM\Column(type="integer", nullable=true) */
    private $m12;



    /**
     * Get the value of id
     */ 
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get the value of rana
     */ 
    public function getRana()
    {
        return $this->rana;
    }

    /**
     * Set the value of rana
     *
     * @return  self
     */ 
    public function setRana($rana)
    {
        $this->rana = $rana;

        return $this;
    }

    /**
     * Get the value of value_type
     */ 
    public function getValue_type()
    {
        return $this->value_type;
    }

    /**
     * Set the value of value_type
     *
     * @return  self
     */ 
    public function setValue_type($value_type)
    {
        $this->value_type = $value_type;

        return $this;
    }

    /**
     * Get the value of m1
     */ 
    public function getM1()
    {
        return $this->m1;
    }

    /**
     * Set the value of m1
     *
     * @return  self
     */ 
    public function setM1($m1)
    {
        $this->m1 = $m1;

        return $this;
    }

    /**
     * Get the value of m2
     */ 
    public function getM2()
    {
        return $this->m2;
    }

    /**
     * Set the value of m2
     *
     * @return  self
     */ 
    public function setM2($m2)
    {
        $this->m2 = $m2;

        return $this;
    }

    /**
     * Get the value of m3
     */ 
    public function getM3()
    {
        return $this->m3;
    }

    /**
     * Set the value of m3
     *
     * @return  self
     */ 
    public function setM3($m3)
    {
        $this->m3 = $m3;

        return $this;
    }

    /**
     * Get the value of m4
     */ 
    public function getM4()
    {
        return $this->m4;
    }

    /**
     * Set the value of m4
     *
     * @return  self
     */ 
    public function setM4($m4)
    {
        $this->m4 = $m4;

        return $this;
    }

    /**
     * Get the value of m5
     */ 
    public function getM5()
    {
        return $this->m5;
    }

    /**
     * Set the value of m5
     *
     * @return  self
     */ 
    public function setM5($m5)
    {
        $this->m5 = $m5;

        return $this;
    }

    /**
     * Get the value of m6
     */ 
    public function getM6()
    {
        return $this->m6;
    }

    /**
     * Set the value of m6
     *
     * @return  self
     */ 
    public function setM6($m6)
    {
        $this->m6 = $m6;

        return $this;
    }

    /**
     * Get the value of m7
     */ 
    public function getM7()
    {
        return $this->m7;
    }

    /**
     * Set the value of m7
     *
     * @return  self
     */ 
    public function setM7($m7)
    {
        $this->m7 = $m7;

        return $this;
    }

    /**
     * Get the value of m8
     */ 
    public function getM8()
    {
        return $this->m8;
    }

    /**
     * Set the value of m8
     *
     * @return  self
     */ 
    public function setM8($m8)
    {
        $this->m8 = $m8;

        return $this;
    }

    /**
     * Get the value of m9
     */ 
    public function getM9()
    {
        return $this->m9;
    }

    /**
     * Set the value of m9
     *
     * @return  self
     */ 
    public function setM9($m9)
    {
        $this->m9 = $m9;

        return $this;
    }

    /**
     * Get the value of m10
     */ 
    public function getM10()
    {
        return $this->m10;
    }

    /**
     * Set the value of m10
     *
     * @return  self
     */ 
    public function setM10($m10)
    {
        $this->m10 = $m10;

        return $this;
    }

    /**
     * Get the value of m11
     */ 
    public function getM11()
    {
        return $this->m11;
    }

    /**
     * Set the value of m11
     *
     * @return  self
     */ 
    public function setM11($m11)
    {
        $this->m11 = $m11;

        return $this;
    }

    /**
     * Get the value of m12
     */ 
    public function getM12()
    {
        return $this->m12;
    }

    /**
     * Set the value of m12
     *
     * @return  self
     */ 
    public function setM12($m12)
    {
        $this->m12 = $m12;

        return $this;
    }

    /**
     * Get the value of timeslot
     */ 
    public function getTimeslot()
    {
        return $this->timeslot;
    }

    /**
     * Set the value of timeslot
     *
     * @return  self
     */ 
    public function setTimeslot($timeslot)
    {
        $this->timeslot = $timeslot;

        return $this;
    }
}
