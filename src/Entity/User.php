<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: "`user`")]
#[ORM\UniqueConstraint(name: "UNIQ_IDENTIFIER_EMAIL", fields: ["email"])]
#[
  UniqueEntity(
    fields: ["email"],
    message: "There is already an account with this email"
  )
]
class User implements UserInterface, PasswordAuthenticatedUserInterface {
  use TimestampableEntity;

  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column]
  private ?int $id = null;

  #[ORM\Column(length: 180)]
  private ?string $email = null;

  /**
   * @var list<string> The user roles
   */
  #[ORM\Column]
  private array $roles = [];

  /**
   * @var string The hashed password
   */
  #[ORM\Column]
  private ?string $password = null;

  #[ORM\Column(length: 100)]
  private ?string $first_name = null;

  #[ORM\Column(length: 100)]
  private ?string $last_name = null;

  /**
   * @var Collection<int, Task>
   */
  #[
    ORM\OneToMany(
      targetEntity: Task::class,
      mappedBy: "createdBy",
      orphanRemoval: true
    )
  ]
  private Collection $createdTasks;

  /**
   * @var Collection<int, Task>
   */
  #[ORM\OneToMany(targetEntity: Task::class, mappedBy: "assignedTo")]
  private Collection $assignedTasks;

  #[ORM\Column]
  private bool $isVerified = false;

  public function __construct() {
    $this->createdTasks = new ArrayCollection();
    $this->assignedTasks = new ArrayCollection();
    $this->teams = new ArrayCollection();
    $this->appNotifications = new ArrayCollection();
    $this->comments = new ArrayCollection();
  }

  public function getId(): ?int {
    return $this->id;
  }

  public function getEmail(): ?string {
    return $this->email;
  }

  public function setEmail(string $email): static {
    $this->email = $email;

    return $this;
  }

  /**
   * A visual identifier that represents this user.
   *
   * @see UserInterface
   */
  public function getUserIdentifier(): string {
    return (string) $this->email;
  }

  /**
   * @see UserInterface
   */
  public function getRoles(): array {
    $roles = $this->roles;
    // guarantee every user at least has ROLE_USER
    $roles[] = "ROLE_USER";

    return array_unique($roles);
  }

  /**
   * @param list<string> $roles
   */
  public function setRoles(array $roles): static {
    $this->roles = $roles;

    return $this;
  }

  /**
   * @see PasswordAuthenticatedUserInterface
   */
  public function getPassword(): ?string {
    return $this->password;
  }

  public function setPassword(string $password): static {
    $this->password = $password;

    return $this;
  }

  /**
   * Ensure the session doesn't contain actual password hashes by CRC32C-hashing them, as supported since Symfony 7.3.
   */
  public function __serialize(): array {
    $data = (array) $this;
    $data["\0" . self::class . "\0password"] = hash("crc32c", $this->password);

    return $data;
  }

  #[\Deprecated]
  public function eraseCredentials(): void {
    // @deprecated, to be removed when upgrading to Symfony 8
  }

  public function getFirstName(): ?string {
    return $this->first_name;
  }

  public function getFullName(): ?string {
    return $this->first_name . " " . $this->last_name;
  }

  public function setFirstName(string $first_name): static {
    $this->first_name = $first_name;

    return $this;
  }

  public function getLastName(): ?string {
    return $this->last_name;
  }

  public function setLastName(string $last_name): static {
    $this->last_name = $last_name;

    return $this;
  }

  /**
   * @return Collection<int, Task>
   */
  public function getCreatedTasks(): Collection {
    return $this->createdTasks;
  }

  public function addCreatedTask(Task $createdTask): static {
    if (!$this->createdTasks->contains($createdTask)) {
      $this->createdTasks->add($createdTask);
      $createdTask->setCreatedBy($this);
    }

    return $this;
  }

  public function removeCreatedTask(Task $createdTask): static {
    if ($this->createdTasks->removeElement($createdTask)) {
      // set the owning side to null (unless already changed)
      if ($createdTask->getCreatedBy() === $this) {
        $createdTask->setCreatedBy(null);
      }
    }

    return $this;
  }

  /**
   * @return Collection<int, Task>
   */
  public function getAssignedTasks(): Collection {
    return $this->assignedTasks;
  }

  public function addAssignedTask(Task $assignedTask): static {
    if (!$this->assignedTasks->contains($assignedTask)) {
      $this->assignedTasks->add($assignedTask);
      $assignedTask->setAssignedTo($this);
    }

    return $this;
  }

  public function removeAssignedTask(Task $assignedTask): static {
    if ($this->assignedTasks->removeElement($assignedTask)) {
      // set the owning side to null (unless already changed)
      if ($assignedTask->getAssignedTo() === $this) {
        $assignedTask->setAssignedTo(null);
      }
    }

    return $this;
  }

  public function isVerified(): bool {
    return $this->isVerified;
  }

  public function setIsVerified(bool $isVerified): static {
    $this->isVerified = $isVerified;

    return $this;
  }

  #[ORM\ManyToMany(targetEntity: Team::class, inversedBy: "users")]
  #[ORM\JoinTable(name: "user_team")]
  private Collection $teams;

  /**
   * @var Collection<int, AppNotification>
   */
  #[
    ORM\OneToMany(
      targetEntity: AppNotification::class,
      mappedBy: "recipient",
      orphanRemoval: true
    )
  ]
  private Collection $appNotifications;

  /**
   * @var Collection<int, Comment>
   */
  #[
    ORM\OneToMany(
      targetEntity: Comment::class,
      mappedBy: "author",
      orphanRemoval: true
    )
  ]
  private Collection $comments;

  /** @return Collection<int, Team> */
  public function getTeams(): Collection {
    return $this->teams;
  }

  public function addTeam(Team $team): self {
    if (!$this->teams->contains($team)) {
      $this->teams[] = $team;
      $team->addUser($this); // keep both sides in sync
    }
    return $this;
  }

  public function removeTeam(Team $team): self {
    if ($this->teams->removeElement($team)) {
      $team->removeUser($this);
    }
    return $this;
  }

  /**
   * @return Collection<int, AppNotification>
   */
  public function getAppNotifications(): Collection {
    return $this->appNotifications;
  }

  public function addAppNotification(AppNotification $appNotification): static {
    if (!$this->appNotifications->contains($appNotification)) {
      $this->appNotifications->add($appNotification);
      $appNotification->setRecipient($this);
    }

    return $this;
  }

  public function removeAppNotification(
    AppNotification $appNotification
  ): static {
    if ($this->appNotifications->removeElement($appNotification)) {
      // set the owning side to null (unless already changed)
      if ($appNotification->getRecipient() === $this) {
        $appNotification->setRecipient(null);
      }
    }

    return $this;
  }

  public function getUnreadNotificationsCount(): int {
    return $this->appNotifications
      ->filter(fn(AppNotification $notification) => !$notification->isRead())
      ->count();
  }

  /**
   * @return Collection<int, Comment>
   */
  public function getComments(): Collection {
    return $this->comments;
  }

  public function addComment(Comment $comment): static {
    if (!$this->comments->contains($comment)) {
      $this->comments->add($comment);
      $comment->setAuthor($this);
    }

    return $this;
  }

  public function removeComment(Comment $comment): static {
    if ($this->comments->removeElement($comment)) {
      // set the owning side to null (unless already changed)
      if ($comment->getAuthor() === $this) {
        $comment->setAuthor(null);
      }
    }

    return $this;
  }
}
