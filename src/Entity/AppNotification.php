<?php

namespace App\Entity;

use App\Repository\AppNotificationRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

#[ORM\Entity(repositoryClass: AppNotificationRepository::class)]
class AppNotification {
  use TimestampableEntity;

  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column]
  private ?int $id = null;

  #[ORM\Column(length: 255)]
  private ?string $message = null;

  #[ORM\Column(length: 255)]
  private ?string $link = null;

  #[ORM\ManyToOne(inversedBy: "appNotifications")]
  #[ORM\JoinColumn(nullable: false)]
  private ?User $recipient = null;

  #[ORM\Column]
  private ?bool $isRead = null;

  public function getId(): ?int {
    return $this->id;
  }

  public function getMessage(): ?string {
    return $this->message;
  }

  public function setMessage(string $message): static {
    $this->message = $message;

    return $this;
  }

  public function getLink(): ?string {
    return $this->link;
  }

  public function setLink(string $link): static {
    $this->link = $link;

    return $this;
  }

  public function getRecipient(): ?User {
    return $this->recipient;
  }

  public function setRecipient(?User $recipient): static {
    $this->recipient = $recipient;

    return $this;
  }

  public function isRead(): ?bool {
    return $this->isRead;
  }

  public function setIsRead(bool $isRead): static {
    $this->isRead = $isRead;

    return $this;
  }
}
