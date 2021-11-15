<?php

namespace App\Entity;

use App\Repository\GoogleAccountRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=GoogleAccountRepository::class)
 */
class GoogleAccount
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
     * @ORM\OneToOne(targetEntity=User::class, mappedBy="google_account", cascade={"persist", "remove"})
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
            $this->user_id->setGoogleAccount(null);
        }

        // set the owning side of the relation if necessary
        if ($user_id !== null && $user_id->getGoogleAccount() !== $this) {
            $user_id->setGoogleAccount($this);
        }

        $this->user_id = $user_id;

        return $this;
    }
}
