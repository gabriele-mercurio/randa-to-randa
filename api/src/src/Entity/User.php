<?php

namespace App\Entity;

use App\Util\Util;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\Table(name="users")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
     */
    private $id;

    /** @ORM\Column(name="first_name", type="string") */
    private $firstName;

    /** @ORM\Column(name="last_name", type="string") */
    private $lastName;

    /** @ORM\Column(type="string", unique=true) */
    private $email;

    /** @ORM\Column(type="string", length=32) */
    protected $salt;

    /** @ORM\Column(type="string", length=32) */
    private $password;

    /** @ORM\Column(type="json") */
    private $roles = [];

    /** @ORM\Column(name="is_admin", type="boolean", options={"default":false}) */
    private $isAdmin;

    /**
     * @var Collection|Director[]
     *
     * @ORM\OneToMany(targetEntity="Director", mappedBy="user")
     */
    private $directors;

    /**
     * User constructor.
     *
     * @throws Exception
     */
    public function __construct()
    {
        $this->id = Uuid::uuid4();
    }

    public function getId(): string
    {
        return $this->id->toString();
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;
        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;
        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getSalt(): string
    {
        return $this->salt;
    }

    private function setSalt(string $salt): self
    {
        $this->salt = $salt;
        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    private function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
        return $this;
    }

    public function isAdmin(): bool
    {
        return !!$this->isAdmin;
    }

    public function setIsAdmin(bool $isAdmin): self
    {
        $this->isAdmin = !!$isAdmin;
        return $this;
    }

    /**
     * @return Collection|Director[]
     */
    public function getDirectors()
    {
        return $this->directors;
    }

    // Custom methods
    public function getFullName(): string
    {
        return trim($this->firstName . " " . $this->lastName);
    }

    public function securePassword(string $password): self
    {
        $salt = md5(Util::generateCode());
        $ashedPassword = md5($salt . md5($password) . $salt);
        $this->setSalt($salt);
        $this->setPassword($ashedPassword);
        return $this;
    }

    /**
     * Needed for abstract resolve
     */
    public function getUsername(): string
    {
        return $this->getEmail();
    }

    /**
     * Needed for abstract resolve
     */
    public function eraseCredentials()
    {
        // Not needed because there are no sensitive information stored on this object.
    }
}
