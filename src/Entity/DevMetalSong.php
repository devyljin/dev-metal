<?php

namespace App\Entity;

use App\Repository\DevMetalSongRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: DevMetalSongRepository::class)]
class DevMetalSong
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["devMetalSong", "devMetalGroup"])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(["devMetalSong", "devMetalGroup"])]
    private ?string $name = null;

    #[ORM\ManyToOne(inversedBy: 'songs')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(["devMetalSong"])]
    private ?DevMetalGroup $author = null;

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

    public function getAuthor(): ?DevMetalGroup
    {
        return $this->author;
    }

    public function setAuthor(?DevMetalGroup $author): static
    {
        $this->author = $author;

        return $this;
    }
}
