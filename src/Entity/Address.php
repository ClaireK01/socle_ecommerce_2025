<?php

namespace App\Entity;

use App\Repository\AddressRepository;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Knp\DoctrineBehaviors\Model\SoftDeletable\SoftDeletableTrait;
use Symfony\Component\Validator\Constraints as Assert;
use Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface;




#[ORM\Entity(repositoryClass: AddressRepository::class)]
class Address
{
    use SoftDeletableTrait,TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', nullable:true)]
    #[Assert\NotBlank(message: 'Ce champ ne peut pas être vide')]
    private $label;

    #[ORM\Column(type: 'string', nullable:true)]
    #[Assert\NotBlank(message: 'Ce champ ne peut pas être vide')]
    private $firstName;

    #[ORM\Column(type: 'string', nullable:true)]
    #[Assert\NotBlank(message: 'Ce champ ne peut pas être vide')]
    private $lastName;

    #[ORM\Column(type: 'string', nullable:true)]
    #[Assert\NotBlank(message: 'Ce champ ne peut pas être vide')]
    private $address;

    #[ORM\Column(type: 'string', nullable:true)]
    #[Assert\NotBlank(message: 'Ce champ ne peut pas être vide')]
    private $code;

    #[ORM\Column(type: 'string', nullable:true)]
    #[Assert\NotBlank(message: 'Ce champ ne peut pas être vide')]
    private $city;

    #[ORM\Column(type: 'string', nullable:true)]
    #[Assert\NotBlank(message: 'Ce champ ne peut pas être vide')]
    private $phone;

    #[ORM\Column(type: 'text', nullable:true)]
    private $infos;

    #[ORM\ManyToOne(targetEntity: User::class)]
    private $user;

    #[ORM\Column(name:"email", type:"string", nullable:true)]
    private string $email;



    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param mixed $label
     */
    public function setLabel($label): void
    {
        $this->label = $label;
    }

    /**
     * @return mixed
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param mixed $address
     */
    public function setAddress($address): void
    {
        $this->address = $address;
    }

    /**
     * @return mixed
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param mixed $city
     */
    public function setCity($city): void
    {
        $this->city = $city;
    }

    /**
     * @return mixed
     */
    public function getInfos()
    {
        return $this->infos;
    }

    /**
     * @param mixed $infos
     */
    public function setInfos($infos): void
    {
        $this->infos = $infos;
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
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param mixed $code
     */
    public function setCode($code): void
    {
        $this->code = $code;
    }

    /**
     * @return mixed
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param mixed $firstName
     */
    public function setFirstName($firstName): void
    {
        $this->firstName = $firstName;
    }

    /**
     * @return mixed
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param mixed $lastName
     */
    public function setLastName($lastName): void
    {
        $this->lastName = $lastName;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }


}