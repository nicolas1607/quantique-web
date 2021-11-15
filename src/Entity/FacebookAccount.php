<?php

namespace App\Entity;

use App\Repository\FacebookAccountRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=FacebookAccountRepository::class)
 */
class FacebookAccount
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @ORM\OneToOne(targetEntity=User::class, mappedBy="facebook_account", cascade={"persist", "remove"})
     */
    private $user_id;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getUserId(): ?User
    {
        return $this->user_id;
    }

    public function setUserId(?User $user_id): self
    {
        // unset the owning side of the relation if necessary
        if ($user_id === null && $this->user_id !== null) {
            $this->user_id->setFacebookAccount(null);
        }

        // set the owning side of the relation if necessary
        if ($user_id !== null && $user_id->getFacebookAccount() !== $this) {
            $user_id->setFacebookAccount($this);
        }

        $this->user_id = $user_id;

        return $this;
    }
}
