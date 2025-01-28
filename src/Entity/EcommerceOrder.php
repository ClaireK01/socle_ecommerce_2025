<?php

namespace App\Entity;

use App\Repository\EcommerceOrderRepository;
use App\Trait\EcommerceTrait;
use App\Trait\P2PRequestTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\SoftDeletableInterface;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Knp\DoctrineBehaviors\Model\SoftDeletable\SoftDeletableTrait;
use Symfony\Component\Validator\Constraints as Assert;
use Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface;



#[ORM\Entity(repositoryClass: EcommerceOrderRepository::class)]
class EcommerceOrder implements SoftDeletableInterface, TimestampableInterface
{
    use SoftDeletableTrait,TimestampableTrait, EcommerceTrait;

    const STATES  = [ "pending" => 0, "payed" => 1, "confirmed" => 2,  'shipped' => 3, 'done' => 4, "canceled" => 5 ];
    const STATES_LABEL  = [
        "<span>En attente</span>" => 0,
        "<span>Payé</span>" => 1,
        "<span>Confirmé</span>" => 2,
        "<span>Envoyé</span>" => 3,
        "<span>Terminé</span>" => 4,
        "<span>Annulé</span>" => 5
    ];


    const PENDING = 0;
    const PAYED = 1;

    const CONFIRMED = 2;

    const SHIPPED = 3;
    const DONE = 4;
    const CANCELED = 5;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'integer', nullable:true)]
    private $number;

    #[ORM\Column(type: 'integer', nullable:true)]
    private $status;

    #[ORM\OneToMany(mappedBy: "ecommerceOrder", targetEntity: CardProduct::class)]
    private $cardProducts;

    #[ORM\ManyToOne(targetEntity: Shipping::class)]
    private $shipping;

    #[ORM\ManyToOne(targetEntity: PaymentMethod::class)]
    private $payment_method;

    #[ORM\Column(type: 'float', nullable:true)]
    private $total_price_ttc;

    #[ORM\Column(type: 'float', nullable:true)]
    private $total_price_ht;

    #[ORM\Column(type: 'text', nullable:true)]
    private $details;

    #[ORM\Column(type: 'string', nullable:true)]
    #[Assert\NotBlank(message: 'Ce champ ne peut pas être vide')]
    private $shippingFirstName;

    #[ORM\Column(type: 'string', nullable:true)]
    #[Assert\NotBlank(message: 'Ce champ ne peut pas être vide')]
    private $shippingLastName;

    #[ORM\Column(type: 'string', nullable:true)]
    #[Assert\NotBlank(message: 'Ce champ ne peut pas être vide')]
    private $shippingAddressLine;

    #[ORM\Column(type: 'string', nullable:true)]
    #[Assert\NotBlank(message: 'Ce champ ne peut pas être vide')]
    private $shippingCode;


    #[ORM\Column(type: 'string', nullable:true)]
    #[Assert\NotBlank(message: 'Ce champ ne peut pas être vide')]
    private $shippingCity;

    #[ORM\Column(type: 'string', nullable:true)]
    #[Assert\NotBlank(message: 'Ce champ ne peut pas être vide')]
    private $facturationFirstName;

    #[ORM\Column(type: 'string', nullable:true)]
    #[Assert\NotBlank(message: 'Ce champ ne peut pas être vide')]
    private $facturationLastName;

    #[ORM\Column(type: 'string', nullable:true)]
    #[Assert\NotBlank(message: 'Ce champ ne peut pas être vide')]
    private $facturationAddressLine;

    #[ORM\Column(type: 'string', nullable:true)]
    #[Assert\NotBlank(message: 'Ce champ ne peut pas être vide')]
    private $facturationCode;


    #[ORM\Column(type: 'string', nullable:true)]
    #[Assert\NotBlank(message: 'Ce champ ne peut pas être vide')]
    private $facturationCity;

    #[ORM\Column(type: 'string', nullable:true)]
    #[Assert\NotBlank(message: 'Ce champ ne peut pas être vide')]
    private $email;

    #[ORM\Column(type: 'string', nullable:true)]
    #[Assert\NotBlank(message: 'Ce champ ne peut pas être vide')]
    private $phone;

    #[ORM\Column(type: 'text', nullable:true)]
    private $shippingInfos;

    #[ORM\Column(type: 'text', nullable:true)]
    private $facturationInfos;


    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: "orders")]
    private $user;

    public function __construct()
    {
        $this->cardProducts = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * @return mixed
     * @ORM\PrePersist()
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * @param mixed $number
     * @ORM\PrePersist()
     */
    public function setNumber($number): void
    {
        $this->number = $number;
    }

    public function getStatusLabel(){
        return array_search($this->status, self::STATES_LABEL) ?? "";
    }

    /**
     * @return mixed
     */
    public function getDetails()
    {
        return $this->details;
    }

    /**
     * @param mixed $details
     */
    public function setDetails($details): void
    {
        $this->details = $details;
    }

    /**
     * @return mixed
     */
    public function getShippingFirstName()
    {
        return $this->shippingFirstName;
    }

    /**
     * @param mixed $shippingFirstName
     */
    public function setShippingFirstName($shippingFirstName): void
    {
        $this->shippingFirstName = $shippingFirstName;
    }

    /**
     * @return mixed
     */
    public function getShippingLastName()
    {
        return $this->shippingLastName;
    }

    /**
     * @param mixed $shippingLastName
     */
    public function setShippingLastName($shippingLastName): void
    {
        $this->shippingLastName = $shippingLastName;
    }

    /**
     * @return mixed
     */
    public function getShippingCode()
    {
        return $this->shippingCode;
    }

    /**
     * @param mixed $shippingCode
     */
    public function setShippingCode($shippingCode): void
    {
        $this->shippingCode = $shippingCode;
    }

    /**
     * @return mixed
     */
    public function getShippingCity()
    {
        return $this->shippingCity;
    }

    /**
     * @param mixed $shippingCity
     */
    public function setShippingCity($shippingCity): void
    {
        $this->shippingCity = $shippingCity;
    }

    /**
     * @return mixed
     */
    public function getFacturationFirstName()
    {
        return $this->facturationFirstName;
    }

    /**
     * @param mixed $facturationFirstName
     */
    public function setFacturationFirstName($facturationFirstName): void
    {
        $this->facturationFirstName = $facturationFirstName;
    }

    /**
     * @return mixed
     */
    public function getFacturationLastName()
    {
        return $this->facturationLastName;
    }

    /**
     * @param mixed $facturationLastName
     */
    public function setFacturationLastName($facturationLastName): void
    {
        $this->facturationLastName = $facturationLastName;
    }

    /**
     * @return mixed
     */
    public function getFacturationCode()
    {
        return $this->facturationCode;
    }

    /**
     * @param mixed $facturationCode
     */
    public function setFacturationCode($facturationCode): void
    {
        $this->facturationCode = $facturationCode;
    }

    /**
     * @return mixed
     */
    public function getFacturationCity()
    {
        return $this->facturationCity;
    }

    /**
     * @param mixed $facturationCity
     */
    public function setFacturationCity($facturationCity): void
    {
        $this->facturationCity = $facturationCity;
    }

    /**
     * @return mixed
     */
    public function getShippingInfos()
    {
        return $this->shippingInfos;
    }

    /**
     * @param mixed $shippingInfos
     */
    public function setShippingInfos($shippingInfos): void
    {
        $this->shippingInfos = $shippingInfos;
    }

    /**
     * @return mixed
     */
    public function getFacturationInfos()
    {
        return $this->facturationInfos;
    }

    /**
     * @param mixed $facturationInfos
     */
    public function setFacturationInfos($facturationInfos): void
    {
        $this->facturationInfos = $facturationInfos;
    }

    /**
     * @return mixed
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param mixed $phone
     */
    public function setPhone($phone): void
    {
        $this->phone = $phone;
    }

    /**
     * @return mixed
     */
    public function getShippingAddressLine()
    {
        return $this->shippingAddressLine;
    }

    /**
     * @param mixed $shippingAddressLine
     */
    public function setShippingAddressLine($shippingAddressLine): void
    {
        $this->shippingAddressLine = $shippingAddressLine;
    }

    /**
     * @return mixed
     */
    public function getFacturationAddressLine()
    {
        return $this->facturationAddressLine;
    }

    /**
     * @param mixed $facturationAddressLine
     */
    public function setFacturationAddressLine($facturationAddressLine): void
    {
        $this->facturationAddressLine = $facturationAddressLine;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email): void
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user): void
    {
        $this->user = $user;
    }


}