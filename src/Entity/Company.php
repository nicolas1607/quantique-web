<?php

namespace App\Entity;

use App\Repository\CompanyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CompanyRepository::class)
 */
class Company
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
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $address;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $postalCode;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $city;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $numTVA;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $siret;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $url;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $phone;

    /**
     * @ORM\OneToMany(targetEntity=FacebookAccount::class, mappedBy="company")
     */
    private $facebook_account;

    /**
     * @ORM\OneToMany(targetEntity=GoogleAccount::class, mappedBy="company")
     */
    private $google_account;

    /**
     * @ORM\OneToMany(targetEntity=Contract::class, mappedBy="company")
     */
    private $contracts;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, mappedBy="compagnies")
     */
    private $users;

    public function __construct()
    {
        $this->facebook_account = new ArrayCollection();
        $this->google_account = new ArrayCollection();
        $this->contracts = new ArrayCollection();
        $this->users = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function setPostalCode(string $postalCode): self
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getNumTVA(): ?string
    {
        return $this->numTVA;
    }

    public function setNumTVA(?string $numTVA): self
    {
        $this->numTVA = $numTVA;

        return $this;
    }

    public function getSiret(): ?string
    {
        return $this->siret;
    }

    public function setSiret(?string $siret): self
    {
        $this->siret = $siret;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * @return Collection|FacebookAccount[]
     */
    public function getFacebookAccount(): Collection
    {
        return $this->facebook_account;
    }

    public function addFacebookAccount(FacebookAccount $facebookAccount): self
    {
        if (!$this->facebook_account->contains($facebookAccount)) {
            $this->facebook_account[] = $facebookAccount;
            $facebookAccount->setCompany($this);
        }

        return $this;
    }

    public function removeFacebookAccount(FacebookAccount $facebookAccount): self
    {
        if ($this->facebook_account->removeElement($facebookAccount)) {
            // set the owning side to null (unless already changed)
            if ($facebookAccount->getCompany() === $this) {
                $facebookAccount->setCompany(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|GoogleAccount[]
     */
    public function getGoogleAccount(): Collection
    {
        return $this->google_account;
    }

    public function addGoogleAccount(GoogleAccount $googleAccount): self
    {
        if (!$this->google_account->contains($googleAccount)) {
            $this->google_account[] = $googleAccount;
            $googleAccount->setCompany($this);
        }

        return $this;
    }

    public function removeGoogleAccount(GoogleAccount $googleAccount): self
    {
        if ($this->google_account->removeElement($googleAccount)) {
            // set the owning side to null (unless already changed)
            if ($googleAccount->getCompany() === $this) {
                $googleAccount->setCompany(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Contract[]
     */
    public function getContracts(): Collection
    {
        return $this->contracts;
    }

    public function addContract(Contract $contract): self
    {
        if (!$this->contracts->contains($contract)) {
            $this->contracts[] = $contract;
            $contract->setCompany($this);
        }

        return $this;
    }

    public function removeContract(Contract $contract): self
    {
        if ($this->contracts->removeElement($contract)) {
            // set the owning side to null (unless already changed)
            if ($contract->getCompany() === $this) {
                $contract->setCompany(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->addCompany($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->removeElement($user)) {
            $user->removeCompany($this);
        }

        return $this;
    }
}
