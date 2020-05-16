<?php

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="users")
 */
class User
{
    /** 
     * @ORM\Id
     * @ORM\Column(type="integer") */
    private $id;

    /** @ORM\Column(type="string", length=32) */
    private $firstname;

    /** @ORM\Column(type="string", length=32) */
    private $lastname;

    /** @ORM\Column(type="string", length=32) */
    private $email;

}
