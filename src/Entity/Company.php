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
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $phone;

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
     * @ORM\OneToMany(targetEntity=FacebookAccount::class, mappedBy="company")
     */
    private $facebook_account;

    /**
     * @ORM\OneToMany(targetEntity=GoogleAccount::class, mappedBy="company")
     */
    private $google_account;

    /**
     * @ORM\OneToMany(targetEntity=Website::class, mappedBy="company")
     */
    private $websites;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, mappedBy="companies")
     */
    private $users;

    public function __construct()
    {
        $this->facebook_account = new ArrayCollection();
        $this->google_account = new ArrayCollection();
        $this->contracts = new ArrayCollection();
        $this->users = new ArrayCollection();
        $this->typeContracts = new ArrayCollection();
        $this->typeInvoices = new ArrayCollection();
        $this->invoices = new ArrayCollection();
        $this->websites = new ArrayCollection();
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


    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

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
     * @return Collection|Website[]
     */
    public function getWebsites(): Collection
    {
        return $this->websites;
    }

    public function addWebsite(Website $website): self
    {
        if (!$this->websites->contains($website)) {
            $this->websites[] = $website;
            $website->setCompany($this);
        }

        return $this;
    }

    public function removeWebsite(Website $website): self
    {
        if ($this->websites->removeElement($website)) {
            // set the owning side to null (unless already changed)
            if ($website->getCompany() === $this) {
                $website->setCompany(null);
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
