<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\InvoiceRepository;

/**
 * @ORM\Entity(repositoryClass=InvoiceRepository::class)
 */
class Invoice
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $releasedAt;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $files;

    /**
     * @ORM\ManyToOne(targetEntity=TypeInvoice::class, inversedBy="invoices")
     * @ORM\JoinColumn(nullable=false)
     */
    private $type;

    /**
     * @ORM\ManyToOne(targetEntity=Company::class, inversedBy="invoices")
     * @ORM\JoinColumn(nullable=false)
     */
    private $company;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $file;

    public function __construct()
    {
        $this->files = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReleasedAt(): ?\DateTime
    {
        return $this->releasedAt;
    }

    public function setReleasedAt(\DateTime $releasedAt): self
    {
        $this->releasedAt = $releasedAt;

        return $this;
    }

    /**
     * @return Collection|String[]
     */
    public function getFiles(): ?string
    {
        return $this->files->first();
    }

    public function addFile(String $file): self
    {
        if (!$this->files->contains($file)) {
            $this->files[] = $file;
        }

        return $this;
    }

    public function removeFile(String $file): self
    {
        $this->files->removeElement($file);
        return $this;
    }

    // public function setFile(string $file): self
    // {
    //     $this->file = $file;

    //     return $this;
    // }

    public function getType(): ?TypeInvoice
    {
        return $this->type;
    }

    public function setType(?TypeInvoice $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): self
    {
        $this->company = $company;

        return $this;
    }

    public function getFile(): ?string
    {
        return $this->file;
    }

    public function setFile(string $file): self
    {
        $this->file = $file;

        return $this;
    }
}
