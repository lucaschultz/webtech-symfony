<?php

namespace App\Entity;

use App\Repository\CommentRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

#[ORM\Entity(repositoryClass: CommentRepository::class)]
class Comment {
  use TimestampableEntity;
  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column]
  private ?int $id = null;

  #[ORM\Column(type: Types::TEXT)]
  private ?string $content = null;

  #[ORM\ManyToOne(inversedBy: "comments")]
  #[ORM\JoinColumn(nullable: false)]
  private ?User $author = null;

  #[ORM\ManyToOne(targetEntity: Task::class, inversedBy: "comments")]
  #[ORM\JoinColumn(nullable: false)]
  private ?Task $task = null;

  public function getId(): ?int {
    return $this->id;
  }

  public function getContent(): ?string {
    return $this->content;
  }

  public function setContent(string $content): static {
    $this->content = $content;

    return $this;
  }

  public function getAuthor(): ?User {
    return $this->author;
  }

  public function setAuthor(?User $author): static {
    $this->author = $author;

    return $this;
  }

  public function getTask(): ?Task {
    return $this->task;
  }

  public function setTask(?Task $task): self {
    $this->task = $task;

    return $this;
  }
}
