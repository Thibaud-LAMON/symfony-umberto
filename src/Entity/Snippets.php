<?php

namespace App\Entity;

use App\Repository\SnippetsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SnippetsRepository::class)]
class Snippets
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $snippet = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $truncated = null;

    #[ORM\ManyToOne(inversedBy: 'snippets')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Ideas $ideas = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSnippet(): ?string
    {
        return $this->snippet;
    }

    public function setSnippet(string $snippet): self
    {
        $this->snippet = $snippet;

        return $this;
    }

    public function getTruncated(): ?string
    {
        return $this->truncated;
    }

    public function setTruncated(?string $truncated): self
    {
        $this->truncated = $truncated;

        return $this;
    }

    public function getIdeas(): ?Ideas
    {
        return $this->ideas;
    }

    public function setIdeas(?Ideas $ideas): self
    {
        $this->ideas = $ideas;

        return $this;
    }
}
