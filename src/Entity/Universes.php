<?php

namespace App\Entity;

use App\Repository\UniversesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UniversesRepository::class)]
class Universes
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\ManyToOne(inversedBy: 'universes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Projects $projects = null;

    #[ORM\OneToMany(mappedBy: 'universes', targetEntity: Branches::class, orphanRemoval: true)]
    private Collection $branches;

    public function __construct()
    {
        $this->branches = new ArrayCollection();
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

    public function getProjects(): ?Projects
    {
        return $this->projects;
    }

    public function setProjects(?Projects $projects): self
    {
        $this->projects = $projects;

        return $this;
    }

    /**
     * @return Collection<int, Branches>
     */
    public function getBranches(): Collection
    {
        return $this->branches;
    }

    public function addBranch(Branches $branch): self
    {
        if (!$this->branches->contains($branch)) {
            $this->branches->add($branch);
            $branch->setUniverses($this);
        }

        return $this;
    }

    public function removeBranch(Branches $branch): self
    {
        if ($this->branches->removeElement($branch)) {
            // set the owning side to null (unless already changed)
            if ($branch->getUniverses() === $this) {
                $branch->setUniverses(null);
            }
        }

        return $this;
    }
}
