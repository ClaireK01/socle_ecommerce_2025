<?php

namespace App\Trait;

use Doctrine\ORM\Mapping as ORM;

trait UserTrait
{

    #[ORM\Column(length: 180, unique: true, nullable:true)]
    protected ?string $email = null;

    #[ORM\Column(length: 180, nullable:true)]
    protected ?string $password = null;

    #[ORM\Column(length: 180, nullable:true)]
    protected ?string $plainPassword = null;

    #[ORM\Column(length: 180, unique: true, nullable:true)]
    protected ?string $username = null;

    #[ORM\Column(length: 180, unique: true, nullable:true)]
    protected ?string $emailCanonical = null;

    #[ORM\Column(length: 180, unique: true, nullable:true)]
    protected ?string $usernameCanonical = null;

    #[ORM\Column]
    protected ?array $roles = [];

    #[ORM\Column(type: "boolean", unique: true, nullable:true)]
    protected ?string $enabled = null;

    #[ORM\Column(length: 180, unique: true, nullable:true)]
    protected ?string $confirmationToken = null;

    #[ORM\Column(type: 'datetime', unique: true, nullable:true)]
    protected $lastLogin = null;


    #[ORM\Column(length: 180, unique: true, nullable:true)]
    protected ?string $salt = null;

    #[ORM\Column(type: 'datetime', unique: true, nullable:true)]
    protected $passwordRequestedAt = null;

    #[ORM\Column(type: 'boolean', unique: true, nullable:true)]
    protected ?bool $passwordRequestNonExpired;

    public function getId()
    {
        return $this->id;
    }

    public function isSuperAdmin()
    {
        return in_array('ROLE_SUPER_ADMIN', $this->roles);
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function setSuperAdmin($role)
    {
        if(!in_array("ROLE_SUPER_ADMIN", $this->roles)){
            $this->roles[] = "ROLE_SUPER_ADMIN";
        }

        return $this;
    }

    public function setPasswordRequestedAt(?\DateTime $date = null)
    {
        $this->passwordRequestedAt = $date;
    }

    public function isPasswordRequestNonExpired($ttl)
    {
        $this->passwordRequestNonExpired = $ttl;
    }

    public function hasRole($role)
    {
        return in_array($role, $this->roles);
    }

    public function addRole($role)
    {
        if(!in_array($role, $this->roles)){
            $this->roles[] = $role;
        }
    }

    public function removeRole($role)
    {
        $arr = array_filter($this->roles, function ($e) use ($role){
            return $e != $role;
        });

        $this->roles = array_values($arr);
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword($password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword($plainPassword)
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    public function getEmailCanonical(): ?string
    {
        return $this->emailCanonical;
    }

    public function setEmailCanonical($emailCanonical)
    {
        $this->emailCanonical = $emailCanonical;

        return $this;
    }

    public function getUsernameCanonical(): ?string
    {
        return $this->usernameCanonical;
    }

    public function setUsernameCanonical($usernameCanonical)
    {
        $this->usernameCanonical = $usernameCanonical;

        return $this;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles)
    {
        $this->roles = $roles;

        return $this;
    }

    public function isEnabled(): ?bool
    {
        return $this->enabled;
    }

    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function getConfirmationToken(): ?string
    {
        return $this->confirmationToken;
    }

    public function setConfirmationToken($confirmationToken)
    {
        $this->confirmationToken = $confirmationToken;
    }

    public function getLastLogin()
    {
        return $this->lastLogin;
    }

    public function setLastLogin(?\DateTime $time = null)
    {
        $this->lastLogin = $time;

        return $this;
    }

    public function getSalt(): ?string
    {
        return $this->salt;
    }

    public function setSalt($salt)
    {
        $this->salt = $salt;

        return $this;
    }
}