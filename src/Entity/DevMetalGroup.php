<?php

namespace App\Entity;

use App\Repository\DevMetalGroupRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
#[ORM\Entity(repositoryClass: DevMetalGroupRepository::class)]
class DevMetalGroup
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["devMetalSong", "devMetalGroup"])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(["devMetalSong", "devMetalGroup"])]
    #[Assert\Length(
        min: 5,
        max: 50,
        minMessage: 'Your dev metal group name must be at least {{ limit }} characters long, or {{ value }} is {{ value_length }} long',
        maxMessage: 'Your first name cannot be longer than {{ limit }} characters',
    )]
    #[Assert\NotEqualTo('LED')]
    private ?string $name = null;

    /**
     * @var Collection<int, DevMetalSong>
     */
    #[ORM\OneToMany(targetEntity: DevMetalSong::class, mappedBy: 'author')]
    #[Groups(["devMetalGroup"])]
    private Collection $songs;

    public function __construct()
    {
        $this->songs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, DevMetalSong>
     */
    public function getSongs(): Collection
    {
        return $this->songs;
    }

    public function addSong(DevMetalSong $song): static
    {
        if (!$this->songs->contains($song)) {
            $this->songs->add($song);
            $song->setAuthor($this);
        }

        return $this;
    }

    public function removeSong(DevMetalSong $song): static
    {
        if ($this->songs->removeElement($song)) {
            // set the owning side to null (unless already changed)
            if ($song->getAuthor() === $this) {
                $song->setAuthor(null);
            }
        }

        return $this;
    }
}
