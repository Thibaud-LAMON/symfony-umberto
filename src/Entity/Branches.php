<?php

namespace App\Entity;

use App\Repository\BranchesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BranchesRepository::class)]
class Branches
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\ManyToOne(inversedBy: 'branches')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Universes $universes = null;

    #[ORM\OneToMany(mappedBy: 'branches', targetEntity: Ideas::class)]
    private Collection $ideas;

    public function __construct()
    {
        $this->ideas = new ArrayCollection();
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

    public function getUniverses(): ?Universes
    {
        return $this->universes;
    }

    public function setUniverses(?Universes $universes): self
    {
        $this->universes = $universes;

        return $this;
    }

    /**
     * @return Collection<int, Ideas>
     */
    public function getIdeas(): Collection
    {
        return $this->ideas;
    }

    public function addIdea(Ideas $idea): self
    {
        if (!$this->ideas->contains($idea)) {
            $this->ideas->add($idea);
            $idea->setBranches($this);
        }

        return $this;
    }

    public function removeIdea(Ideas $idea): self
    {
        if ($this->ideas->removeElement($idea)) {
            // set the owning side to null (unless already changed)
            if ($idea->getBranches() === $this) {
                $idea->setBranches(null);
            }
        }

        return $this;
    }
}
