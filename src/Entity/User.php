<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $password;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @ORM\OneToMany(targetEntity=Roles::class, mappedBy="user", cascade = {"persist", "remove"})
     */
    private $organization_role;

    public function __construct()
    {
        $this->organization_role = new ArrayCollection();
    }

    public function getRoles(): array
    {
        $roles = $this->roles;

        $roles[] = 'ROLE_USER';

        return $roles;
    }

    public function setRoles(array $roles): array
    {
        $roles = $this->roles;

        $roles[] = 'ROLE_USER';

        return $roles;
    }

    public function getSalt()
    {
        // TODO: Implement getSalt() method.
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    /**
     * @return Collection|Roles[]
     */
    public function getOrganizationRole(): Collection
    {
        return $this->organization_role;
    }

    public function addOrganizationRole(Roles $organizationRole): self
    {
        if (!$this->organization_role->contains($organizationRole)) {
            $this->organization_role[] = $organizationRole;
            $organizationRole->setUser($this);
        }

        return $this;
    }

    public function removeOrganizationRole(Roles $organizationRole): self
    {
        if ($this->organization_role->contains($organizationRole)) {
            $this->organization_role->removeElement($organizationRole);
            // set the owning side to null (unless already changed)
            if ($organizationRole->getUser() === $this) {
                $organizationRole->setUser(null);
            }
        }

        return $this;
    }
}
