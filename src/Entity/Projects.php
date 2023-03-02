<?php

namespace App\Entity;

use App\Repository\ProjectsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProjectsRepository::class)]
class Projects
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\ManyToOne(inversedBy: 'projects')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Users $users = null;

    #[ORM\OneToMany(mappedBy: 'projects', targetEntity: Universes::class)]
    private Collection $universes;

    public function __construct()
    {
        $this->universes = new ArrayCollection();
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

    public function getUsers(): ?Users
    {
        return $this->users;
    }

    public function setUsers(?Users $users): self
    {
        $this->users = $users;

        return $this;
    }

    /**
     * @return Collection<int, Universes>
     */
    public function getUniverses(): Collection
    {
        return $this->universes;
    }

    public function addUniverse(Universes $universe): self
    {
        if (!$this->universes->contains($universe)) {
            $this->universes->add($universe);
            $universe->setProjects($this);
        }

        return $this;
    }

    public function removeUniverse(Universes $universe): self
    {
        if ($this->universes->removeElement($universe)) {
            // set the owning side to null (unless already changed)
            if ($universe->getProjects() === $this) {
                $universe->setProjects(null);
            }
        }

        return $this;
    }
}
