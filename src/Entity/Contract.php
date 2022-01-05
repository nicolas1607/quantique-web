<?php

namespace App\Entity;

use App\Repository\ContractRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ContractRepository::class)
 */
class Contract
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="float")
     */
    private $price;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $promotion;

    /**
     * @ORM\ManyToOne(targetEntity=TypeContract::class, inversedBy="contracts")
     * @ORM\JoinColumn(nullable=false)
     */
    private $type;

    /**
     * @ORM\ManyToOne(targetEntity=Website::class, inversedBy="contracts")
     * @ORM\JoinColumn(nullable=false)
     */
    private $website;

    /**
     * @ORM\OneToMany(targetEntity=Note::class, mappedBy="contract", orphanRemoval=true)
     */
    private $notes;

    public function __construct()
    {
        $this->invoices = new ArrayCollection();
        $this->notes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getPromotion(): ?float
    {
        return $this->promotion;
    }

    public function setPromotion(?float $promotion): self
    {
        $this->promotion = $promotion;

        return $this;
    }

    public function getType(): ?TypeContract
    {
        return $this->type;
    }

    public function setType(?TypeContract $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getWebsite(): ?Website
    {
        return $this->website;
    }

    public function setWebsite(?Website $website): self
    {
        $this->website = $website;

        return $this;
    }

    /**
     * @return Collection|Note[]
     */
    public function getNotes(): Collection
    {
        return $this->notes;
    }

    public function addNote(Note $note): self
    {
        if (!$this->notes->contains($note)) {
            $this->notes[] = $note;
            $note->setContract($this);
        }

        return $this;
    }

    public function removeNote(Note $note): self
    {
        if ($this->notes->removeElement($note)) {
            // set the owning side to null (unless already changed)
            if ($note->getContract() === $this) {
                $note->setContract(null);
            }
        }

        return $this;
    }
}
