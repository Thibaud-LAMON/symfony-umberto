<?php

namespace App\Entity;

use App\Repository\IdeasRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: IdeasRepository::class)]
class Ideas
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\ManyToOne(inversedBy: 'ideas')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Branches $branches = null;

    #[ORM\OneToMany(mappedBy: 'ideas', targetEntity: Suggestions::class)]
    private Collection $suggestions;

    #[ORM\OneToMany(mappedBy: 'ideas', targetEntity: Snippets::class)]
    private Collection $snippets;

    public function __construct()
    {
        $this->suggestions = new ArrayCollection();
        $this->snippets = new ArrayCollection();
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

    public function getBranches(): ?Branches
    {
        return $this->branches;
    }

    public function setBranches(?Branches $branches): self
    {
        $this->branches = $branches;

        return $this;
    }

    /**
     * @return Collection<int, Suggestions>
     */
    public function getSuggestions(): Collection
    {
        return $this->suggestions;
    }

    public function addSuggestion(Suggestions $suggestion): self
    {
        if (!$this->suggestions->contains($suggestion)) {
            $this->suggestions->add($suggestion);
            $suggestion->setIdeas($this);
        }

        return $this;
    }

    public function removeSuggestion(Suggestions $suggestion): self
    {
        if ($this->suggestions->removeElement($suggestion)) {
            // set the owning side to null (unless already changed)
            if ($suggestion->getIdeas() === $this) {
                $suggestion->setIdeas(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Snippets>
     */
    public function getSnippets(): Collection
    {
        return $this->snippets;
    }

    public function addSnippet(Snippets $snippet): self
    {
        if (!$this->snippets->contains($snippet)) {
            $this->snippets->add($snippet);
            $snippet->setIdeas($this);
        }

        return $this;
    }

    public function removeSnippet(Snippets $snippet): self
    {
        if ($this->snippets->removeElement($snippet)) {
            // set the owning side to null (unless already changed)
            if ($snippet->getIdeas() === $this) {
                $snippet->setIdeas(null);
            }
        }

        return $this;
    }
}
