<?php

namespace App\Entity;

use App\Repository\SynonymsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SynonymsRepository::class)]
class Synonyms
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 511)]
    private ?string $synonym = null;

    #[ORM\ManyToOne(inversedBy: 'synonyms')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Ideas $ideas = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSynonym(): ?string
    {
        return $this->synonym;
    }

    public function setSynonym(string $synonym): self
    {
        $this->synonym = $synonym;

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
