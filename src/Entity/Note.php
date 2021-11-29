<?php

namespace App\Entity;

use App\Repository\NoteRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=NoteRepository::class)
 */
class Note
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=4600)
     */
    private $message;

    /**
     * @ORM\ManyToOne(targetEntity=Contract::class, inversedBy="notes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $contract;

    /**
     * @ORM\Column(type="datetime")
     */
    private $releasedAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getContract(): ?Contract
    {
        return $this->contract;
    }

    public function setContract(?Contract $contract): self
    {
        $this->contract = $contract;

        return $this;
    }

    public function getReleasedAt(): ?\DateTimeInterface
    {
        return $this->releasedAt;
    }

    public function setReleasedAt(\DateTimeInterface $releasedAt): self
    {
        $this->releasedAt = $releasedAt;

        return $this;
    }
}
