<?php

namespace App\Entity;

use App\Repository\AgreementRepository;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use App\Repository\RolesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @ORM\Entity(repositoryClass=AgreementRepository::class)
 * @Vich\Uploadable
 */
class Agreement
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Roles::class, inversedBy="agreements")
     * @ORM\JoinColumn(name="role_id", referencedColumnName="id",nullable=false)
     */
    private $role;

    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     *
     * @Vich\UploadableField(mapping="agreement", fileNameProperty="agreementName")
     *
     * @var File|null
     */
    private $agreementFile;

    /**
     * @ORM\Column(type="string", nullable=true)
     *
     * @var string|null
     */
    private $agreementName;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @var DateTimeInterface|null
     */
    private $updatedAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRole(): ?Roles
    {
        return $this->role;
    }

    public function setRole(?Roles $role): self
    {
        $this->role = $role;

        return $this;
    }

    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile|null $agreementFile
     */
    public function setAgreementFile(?File $agreementFile = null): void
    {
        $this->agreementFile = $agreementFile;

        if (null !== $agreementFile) {
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getAgreementFile(): ?File
    {
        return $this->agreementFile;
    }

    public function setAgreementName(?string $agreementName): void
    {
        $this->agreementName = $agreementName;
    }

    public function getAgreementName(): ?string
    {
        return $this->agreementName;
    }
}
