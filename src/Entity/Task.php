<?php

namespace App\Entity;

use App\Constant\TaskPriority;
use App\Constant\TaskStatus;
use App\Repository\TaskRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: TaskRepository::class)]
class Task {
  use TimestampableEntity;

  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column]
  private ?int $id = null;

  #[ORM\Column(length: 255)]
  private ?string $title = null;

  #[ORM\Column(type: Types::TEXT, nullable: true)]
  private ?string $description = null;

  #[ORM\Column(enumType: TaskStatus::class)]
  private ?TaskStatus $status = null;

  #[ORM\Column(enumType: TaskPriority::class)]
  private ?TaskPriority $priority = null;

  #[ORM\Column(nullable: true)]
  private ?\DateTime $deadline = null;

  #[ORM\ManyToOne(inversedBy: "tasks")]
  #[ORM\JoinColumn(nullable: false)]
  private ?Team $team = null;

  #[ORM\ManyToOne(inversedBy: "createdTasks")]
  #[ORM\JoinColumn(nullable: false)]
  private ?User $createdBy = null;

  #[ORM\ManyToOne(inversedBy: "assignedTasks")]
  private ?User $assignedTo = null;

  #[
    ORM\OneToMany(
      mappedBy: "task",
      targetEntity: Comment::class,
      orphanRemoval: true,
      cascade: ["persist", "remove"]
    )
  ]
  private Collection $comments;

  public function __construct() {
    $this->comments = new ArrayCollection();
  }

  public function getId(): ?int {
    return $this->id;
  }

  public function getTitle(): ?string {
    return $this->title;
  }

  public function setTitle(string $title): static {
    $this->title = $title;

    return $this;
  }

  public function getDescription(): ?string {
    return $this->description;
  }

  public function setDescription(?string $description): static {
    $this->description = $description;

    return $this;
  }

  public function getStatus(): ?TaskStatus {
    return $this->status;
  }

  public function setStatus(TaskStatus $status): static {
    $this->status = $status;

    return $this;
  }

  public function getPriority(): ?TaskPriority {
    return $this->priority;
  }

  public function setPriority(TaskPriority $priority): static {
    $this->priority = $priority;

    return $this;
  }

  public function getDeadline(): ?\DateTime {
    return $this->deadline;
  }

  public function setDeadline(?\DateTime $deadline): static {
    $this->deadline = $deadline;

    return $this;
  }

  public function getTeam(): ?Team {
    return $this->team;
  }

  public function setTeam(?Team $team): static {
    $this->team = $team;

    return $this;
  }

  public function getCreatedBy(): ?User {
    return $this->createdBy;
  }

  public function setCreatedBy(?User $createdBy): static {
    $this->createdBy = $createdBy;

    return $this;
  }

  public function getAssignedTo(): ?User {
    return $this->assignedTo;
  }

  public function setAssignedTo(?User $assignedTo): static {
    $this->assignedTo = $assignedTo;

    return $this;
  }

  /**
   * @return User[]
   */
  public function getStakeholders(): array {
    return array_filter([$this->getAssignedTo(), $this->getCreatedBy()]);
  }

  /**
   * @return Collection<int, Comment>
   */
  public function getComments(): Collection {
    return $this->comments;
  }

  public function addComment(Comment $comment): self {
    if (!$this->comments->contains($comment)) {
      $this->comments[] = $comment;
      $comment->setTask($this);
    }
    return $this;
  }

  public function removeComment(Comment $comment): self {
    if ($this->comments->removeElement($comment)) {
      // set the owning side to null (unless already changed)
      if ($comment->getTask() === $this) {
        $comment->setTask(null);
      }
    }
    return $this;
  }
}
