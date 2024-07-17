<?php

namespace App\Entity;

use App\Repository\ResourceIdRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ResourceIdRepository::class)]
class ResourceId
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $timestamp = null;

    #[ORM\Column(type: Types::STRING)]
    private ?string $resourceId = null;

    #[ORM\ManyToOne(inversedBy: 'resourceIds')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getResourceId(): ?string
    {
        return $this->resourceId;
    }

    public function setResourceId(?string $resourceId): void
    {
        $this->resourceId = $resourceId;
    }

    public function getTimestamp(): ?\DateTimeInterface
    {
        return $this->timestamp;
    }

    public function setTimestamp(?\DateTimeInterface $timestamp): void
    {
        $this->timestamp = $timestamp;
    }
}
