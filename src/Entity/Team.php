<?php

namespace App\Entity;

use App\Repository\TeamRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

#[ORM\Entity(repositoryClass: TeamRepository::class)]
class Team {
  use TimestampableEntity;

  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column]
  private ?int $id = null;

  #[ORM\Column(length: 100)]
  private ?string $name = null;

  #[ORM\Column(type: Types::TEXT, nullable: true)]
  private ?string $description = null;

  /**
   * @var Collection<int, Task>
   */
  #[ORM\OneToMany(targetEntity: Task::class, mappedBy: "team")]
  private Collection $tasks;

  public function __construct() {
    $this->tasks = new ArrayCollection();
    $this->users = new ArrayCollection();
  }

  public function getId(): ?int {
    return $this->id;
  }

  public function getName(): ?string {
    return $this->name;
  }

  public function setName(string $name): static {
    $this->name = $name;

    return $this;
  }

  public function getDescription(): ?string {
    return $this->description;
  }

  public function setDescription(?string $description): static {
    $this->description = $description;

    return $this;
  }

  /**
   * @return Collection<int, Task>
   */
  public function getTasks(): Collection {
    return $this->tasks;
  }

  public function addTask(Task $task): static {
    if (!$this->tasks->contains($task)) {
      $this->tasks->add($task);
      $task->setTeam($this);
    }

    return $this;
  }

  public function removeTask(Task $task): static {
    if ($this->tasks->removeElement($task)) {
      // set the owning side to null (unless already changed)
      if ($task->getTeam() === $this) {
        $task->setTeam(null);
      }
    }

    return $this;
  }

  #[ORM\ManyToMany(targetEntity: User::class, mappedBy: "teams")]
  private Collection $users;

  /** @return Collection<int, User> */
  public function getUsers(): Collection {
    return $this->users;
  }

  public function addUser(User $user): self {
    if (!$this->users->contains($user)) {
      $this->users[] = $user;
      $user->addTeam($this); // keep both sides in sync
    }
    return $this;
  }

  public function removeUser(User $user): self {
    if ($this->users->removeElement($user)) {
      $user->removeTeam($this);
    }
    return $this;
  }

  public function hasMember(User $user): bool {
    return $this->getUsers()->contains($user);
  }

  #[ORM\ManyToOne(targetEntity: User::class)]
  #[ORM\JoinColumn(nullable: false)]
  private ?User $createdBy = null;

  public function getCreatedBy(): ?User {
    return $this->createdBy;
  }

  public function setCreatedBy(?User $user): self {
    $this->createdBy = $user;
    return $this;
  }
}
