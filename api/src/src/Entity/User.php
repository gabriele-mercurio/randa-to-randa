<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Swagger\Annotations as SWG;
use Symfony\Component\Security\Core\User\UserInterface;
use RuntimeException;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\Table(name="users")
 * @SWG\Definition()
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @SWG\Property()
     */
    private $id;

    /**
     * @ORM\Column(name="first_name", type="string", length="255")
     * @SWG\Property()
     */
    private $firstName;

    /**
     * @ORM\Column(name="last_name", type="string", length="255")
     * @SWG\Property()
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @SWG\Property()
     */
    private $username;

    /**
     * @ORM\Column(type="json")
     * @SWG\Property(type="array", @SWG\Items(type="string"))
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     * @SWG\Property()
     */
    private $password;

    /**
     * @var string|null
     */
    protected $salt;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getFirstName(): string
    {
        return (string) $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getLastName(): string
    {
        return (string) $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @see UserInterface
     */
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

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    /**
     * @param string $password
     *
     * @throws RuntimeException
     */
    public function setPassword(string $password): self
    {
        $this->plainPassword = $password;

        $encryptedPassword = password_hash($password, PASSWORD_BCRYPT);
        if (false === $encryptedPassword) {
            throw new RuntimeException('Can\'t set the new password');
        }
        $this->password = $encryptedPassword;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): self
    {
        $this->plainPassword = null;

        return $this;
    }

    /**
     * @return bool
     */
    public function isAdmin(): bool
    {
        return in_array("admin", $this->roles);
    }

    /**
     * @return string
     */
    public function getFullName(): string
    {
        return trim($this->firstName . " " . $this->lastName);
    }
}