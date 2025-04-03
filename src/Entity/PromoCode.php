<?php

namespace App\Entity;

use App\Repository\PromoCodeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Knp\DoctrineBehaviors\Model\SoftDeletable\SoftDeletableTrait;
use Symfony\Component\Validator\Constraints as Assert;
use Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface;




#[ORM\Entity(repositoryClass: PromoCodeRepository::class)]
class PromoCode
{
    use SoftDeletableTrait,TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(nullable: true)]
    private ?int $type = null;

    #[ORM\Column(nullable: true)]
    private ?int $application = null;

    #[ORM\ManyToMany(targetEntity: Product::class, inversedBy: 'promoCodes')]
    private Collection $products;

    #[ORM\Column(nullable: true)]
    private ?float $amount = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $expiring_date = null;

    #[ORM\Column(nullable: true)]
    private ?float $min_amount = null;

    #[ORM\Column(type: Types::INTEGER ,nullable: true)]
    private ?float $max_usage= null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $message = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $code = null;

    #[ORM\OneToMany(mappedBy: 'promoCode', targetEntity: Cart::class)]
    private Collection $carts;

    public function __construct()
    {
        $this->products = new ArrayCollection();
        $this->carts = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?int
    {
        return $this->type;
    }

    public function setType(?int $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getApplication(): ?int
    {
        return $this->application;
    }

    public function setApplication(?int $application): static
    {
        $this->application = $application;

        return $this;
    }

    /**
     * @return Collection<int, Product>
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(Product $product): static
    {
        if (!$this->products->contains($product)) {
            $this->products->add($product);
        }

        return $this;
    }

    public function removeProduct(Product $product): static
    {
        $this->products->removeElement($product);

        return $this;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(?float $amount): static
    {
        $this->amount = $amount;

        return $this;
    }

    public function getExpiringDate(): ?\DateTimeInterface
    {
        return $this->expiring_date;
    }

    public function setExpiringDate(?\DateTimeInterface $expiring_date): static
    {
        $this->expiring_date = $expiring_date;

        return $this;
    }

    public function getMinAmount(): ?float
    {
        return $this->min_amount;
    }

    public function setMinAmount(?float $min_amount): static
    {
        $this->min_amount = $min_amount;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): static
    {
        $this->message = $message;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): static
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @return Collection<int, Cart>
     */
    public function getCarts(): Collection
    {
        return $this->carts;
    }

    public function addCart(Cart $cart): static
    {
        if (!$this->carts->contains($cart)) {
            $this->carts->add($cart);
            $cart->setPromoCode($this);
        }

        return $this;
    }

    public function removeCart(Cart $cart): static
    {
        if ($this->carts->removeElement($cart)) {
            // set the owning side to null (unless already changed)
            if ($cart->getPromoCode() === $this) {
                $cart->setPromoCode(null);
            }
        }

        return $this;
    }

    public function getMaxUsage(): ?float
    {
        return $this->max_usage;
    }

    public function setMaxUsage(?float $max_usage): void
    {
        $this->max_usage = $max_usage;
    }

}