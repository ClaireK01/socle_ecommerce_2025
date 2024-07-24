<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Knp\DoctrineBehaviors\Model\SoftDeletable\SoftDeletableTrait;
use Symfony\Component\Validator\Constraints as Assert;
use Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface;




#[ORM\Entity(repositoryClass: OrderRepository::class)]
class EcommerceOrder
{
    use SoftDeletableTrait,TimestampableTrait;


    const PENDING = 0;
    const CONFIRMED = 1;
    const PAYED = 2;
    const CANCELED = 3;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'integer', nullable:true)]
    private $status;

    #[ORM\ManyToOne(targetEntity: Address::class)]
    private $shipping_address;

    #[ORM\ManyToOne(targetEntity: Address::class)]
    private $facturation_address;

    #[ORM\OneToMany(targetEntity: CardProduct::class, mappedBy: "order")]
    private $cardProducts;

    #[ORM\ManyToOne(targetEntity: Shipping::class)]
    private $shipping;

    #[ORM\ManyToOne(targetEntity: PaymentMethod::class)]
    private $payment_method;

    #[ORM\Column(type: 'integer', nullable:true)]
    private $total_price_ttc;

    #[ORM\Column(type: 'integer', nullable:true)]
    private $total_price_ht;


    public function __construct()
    {
        $this->cardProducts = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCardProducts(){
        return $this->cardProducts;
    }

    public function addCardProduct(CardProduct $cardProduct): self
    {
        if (!$this->cardProducts->contains($cardProduct)) {
            $this->cardProducts->add($cardProduct);
            $cardProduct->setEcommerceOrder($this);
        }

        return $this;
    }

    public function removeCardProduct(CardProduct $cardProduct): self
    {
        $this->cardProducts->removeElement($cardProduct);

        return $this;
    }

    public function setCardProducts($cardProducts)
    {
        $this->cardProducts = $cardProducts;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getShippingAddress()
    {
        return $this->shipping_address;
    }

    /**
     * @param mixed $shipping_address
     */
    public function setShippingAddress($shipping_address): void
    {
        $this->shipping_address = $shipping_address;
    }

    /**
     * @return mixed
     */
    public function getTotalPriceTtc()
    {
        return $this->total_price_ttc;
    }

    /**
     * @param mixed $total_price_ttc
     */
    public function setTotalPriceTtc($total_price_ttc): void
    {
        $this->total_price_ttc = $total_price_ttc;
    }

    /**
     * @return mixed
     */
    public function getTotalPriceHt()
    {
        return $this->total_price_ht;
    }

    /**
     * @param mixed $total_price_ht
     */
    public function setTotalPriceHt($total_price_ht): void
    {
        $this->total_price_ht = $total_price_ht;
    }

    /**
     * @return mixed
     */
    public function getFacturationAddress()
    {
        return $this->facturation_address;
    }

    /**
     * @param mixed $facturation_address
     */
    public function setFacturationAddress($facturation_address): void
    {
        $this->facturation_address = $facturation_address;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status): void
    {
        $this->status = $status;
    }

    /**
     * @return mixed
     */
    public function getShipping()
    {
        return $this->shipping;
    }

    /**
     * @param mixed $shipping
     */
    public function setShipping($shipping): void
    {
        $this->shipping = $shipping;
    }

    /**
     * @return mixed
     */
    public function getPaymentMethod()
    {
        return $this->payment_method;
    }

    /**
     * @param mixed $payment_method
     */
    public function setPaymentMethod($payment_method): void
    {
        $this->payment_method = $payment_method;
    }


}