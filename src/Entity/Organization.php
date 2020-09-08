<?php

namespace App\Entity;

use App\Repository\OrganizationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=OrganizationRepository::class)
 */
class Organization
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
    private $name;

    /**
     * @ORM\OneToMany(targetEntity=Roles::class, mappedBy="organization", cascade = {"persist", "remove"})
     */
    private $organization_roles;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $org_wallet;

    public function __construct()
    {
        $this->organization_roles = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection|Roles[]
     */
    public function getOrganizationRoles(): Collection
    {
        return $this->organization_roles;
    }

    public function addOrganizationRole(Roles $organizationRole): self
    {
        if (!$this->organization_roles->contains($organizationRole)) {
            $this->organization_roles[] = $organizationRole;
            $organizationRole->setOrganization($this);
        }

        return $this;
    }

    public function removeOrganizationRole(Roles $organizationRole): self
    {
        if ($this->organization_roles->contains($organizationRole)) {
            $this->organization_roles->removeElement($organizationRole);
            // set the owning side to null (unless already changed)
            if ($organizationRole->getOrganization() === $this) {
                $organizationRole->setOrganization(null);
            }
        }

        return $this;
    }

    public function getOrgWallet(): ?int
    {
        return $this->org_wallet;
    }

    public function setOrgWallet(?int $org_wallet): self
    {
        $this->org_wallet = $org_wallet;

        return $this;
    }
}
