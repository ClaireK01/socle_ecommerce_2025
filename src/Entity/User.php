<?php

namespace App\Entity;

use App\Entity\Fos\BaseUser;
use App\Trait\P2PUserTrait;
use App\Trait\PictureTrait;
use App\Trait\UserTrait;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\JoinTable;
use Doctrine\ORM\Mapping\AssociationOverride;
use Doctrine\ORM\Mapping\AssociationOverrides;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\UserInterface;
use Knp\DoctrineBehaviors\Model\SoftDeletable\SoftDeletableTrait;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use phpDocumentor\Reflection\Types\Boolean;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\VarDumper\VarDumper;


#[ORM\Entity(repositoryClass: UserRepository::class)]
#[AssociationOverrides([
    new AssociationOverride(
        name: "pictures",
        joinColumns: [new JoinColumn(name: "user_id")],
        inverseJoinColumns: [new JoinColumn(name: "picture_id")],
        joinTable: new JoinTable(
            name: "user_picture",
        )
    )
])]
#[ORM\Table(name: 'user')]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements PasswordAuthenticatedUserInterface, UserInterface
{

    use UserTrait, PictureTrait, TimestampableTrait, SoftDeletableTrait;

    const CONNECTED = 1;
    const DISCONNECTED = 0;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    protected ?int $id = null;

    #[ORM\Column(length: 300, nullable: true)]
    private ?string $description;

    #[ORM\Column(name:"status", type:"integer", nullable:true)]
    private ?int $status;

    #[ORM\Column(type: 'boolean')]
    private $isVerified = false;

    #[ORM\OneToMany(mappedBy: 'sender', targetEntity: Conversation::class)]
    private Collection $conversations;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: ResourceId::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $resourceIds;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Address::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $addresses;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: UserSession::class, orphanRemoval: true)]
    private Collection $userSessions;

    public function __construct()
    {
        $this->session_ids = [];
        $this->conversations = new ArrayCollection();
        $this->resourceIds = new ArrayCollection();
        $this->address = new ArrayCollection();
        $this->userSessions = new ArrayCollection();
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }


    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     */
    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return Collection<int, Conversation>
     */
    public function getConversations(): Collection
    {
        return $this->conversations;
    }

    public function addConversation(Conversation $conversation): static
    {
        if (!$this->conversations->contains($conversation)) {
            $this->conversations->add($conversation);
        }

        return $this;
    }

    public function removeConversation(Conversation $conversation): static
    {
        if ($this->conversations->removeElement($conversation)) {
//            if ($conversation->removeMember($this) ) {
                $conversation->removeMember($this);
//            }
        }

        return $this;
    }

    /**
     * @return int
     */
    public function getStatus(): ?int
    {
        return $this->status;
    }

    /**
     * @param int $status
     */
    public function setStatus(?int $status): void
    {
        $this->status = $status;
    }

    /**
     * @return Collection<int, ResourceId>
     */
    public function getResourceIds(): Collection
    {
        return $this->resourceIds;
    }

    public function addResourceId(ResourceId $resourceId): static
    {
        if (!$this->resourceIds->contains($resourceId)) {
            $this->resourceIds->add($resourceId);
            $resourceId->setUser($this);
        }

        return $this;
    }

    public function removeResourceId(ResourceId $resourceId): static
    {
        if ($this->resourceIds->removeElement($resourceId)) {
            // set the owning side to null (unless already changed)
            if ($resourceId->getUser() === $this) {
                $resourceId->setUser(null);
            }
        }

        return $this;
    }

    public function findResourceId($id) : bool{

        foreach ($this->getResourceIds() as $rIds){
            if($rIds->getResourceId() == $id){
                return true;
            }
        }

        return false;
    }

    public function getAddresses(){
        return $this->addresses;
    }

    public function addAddress(Address $address): self
    {
        if (!$this->addresses->contains($address)) {
            $this->addresses->add($address);
            $address->setUser($this);
        }

        return $this;
    }

    public function removeAddress(Address $address): self
    {
        $this->addresses->removeElement($address);

        return $this;
    }

    /**
     * @return Collection<int, UserSession>
     */
    public function getUserSessions(): Collection
    {
        return $this->userSessions;
    }

    public function addUserSession(UserSession $userSession): static
    {
        if (!$this->userSessions->contains($userSession)) {
            $this->userSessions->add($userSession);
            $userSession->setUser($this);
        }

        return $this;
    }

    public function removeUserSession(UserSession $userSession): static
    {
        if ($this->userSessions->removeElement($userSession)) {
            // set the owning side to null (unless already changed)
            if ($userSession->getUser() === $this) {
                $userSession->setUser(null);
            }
        }

        return $this;
    }
}
