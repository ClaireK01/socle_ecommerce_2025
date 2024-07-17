<?php

namespace App\Entity;

use App\Repository\MessageRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\SoftDeletable\SoftDeletableTrait;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;

#[ORM\Entity(repositoryClass: MessageRepository::class)]
class Message implements TimestampableInterface
{

    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;


    #[ORM\ManyToOne(inversedBy: 'conversations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $sender = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $content = null;

    #[ORM\Column(name:"is_read", type:"boolean", nullable:true)]
    private ?bool $is_read = false;

    #[ORM\ManyToOne(inversedBy: 'messages')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Conversation $conversation = null;


    public function getId(): ?int
    {
        return $this->id;
    }


    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getSender(): ?User
    {
        return $this->sender;
    }

    public function setSender(?User $sender): static
    {
        $this->sender = $sender;

        return $this;
    }

    public function getConversation(): ?Conversation
    {
        return $this->conversation;
    }

    public function setConversation(?Conversation $conversation): static
    {
        $this->conversation = $conversation;

        return $this;
    }

    public function getIsRead(): ?bool
    {
        return $this->is_read;
    }

    public function setIsRead(?bool $is_read): void
    {
        $this->is_read = $is_read;
    }
}
