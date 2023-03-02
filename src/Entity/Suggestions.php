<?php

namespace App\Entity;

use App\Repository\SuggestionsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SuggestionsRepository::class)]
class Suggestions
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $suggestion = null;

    #[ORM\ManyToOne(inversedBy: 'suggestions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Ideas $ideas = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSuggestion(): ?string
    {
        return $this->suggestion;
    }

    public function setSuggestion(string $suggestion): self
    {
        $this->suggestion = $suggestion;

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
