<?php

namespace App\Entity;

use App\Repository\TeamJoinRequestRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

#[ORM\Entity(repositoryClass: TeamJoinRequestRepository::class)]
class TeamJoinRequest {
  use TimestampableEntity;

  public const STATUS_PENDING = "PENDING";
  public const STATUS_ACCEPTED = "ACCEPTED";
  public const STATUS_DECLINED = "DECLINED";

  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column]
  private ?int $id = null;

  #[ORM\ManyToOne(inversedBy: "teamJoinRequests")]
  #[ORM\JoinColumn(nullable: false)]
  private ?Team $team = null;

  #[ORM\ManyToOne(inversedBy: "teamJoinRequests")]
  #[ORM\JoinColumn(nullable: false)]
  private ?User $requester = null;

  #[ORM\Column(length: 255)]
  private ?string $status = null;

  public function getId(): ?int {
    return $this->id;
  }

  public function getTeam(): ?Team {
    return $this->team;
  }

  public function setTeam(?Team $team): static {
    $this->team = $team;

    return $this;
  }

  public function getRequester(): ?User {
    return $this->requester;
  }

  public function setRequester(?User $requester): static {
    $this->requester = $requester;

    return $this;
  }

  public function getStatus(): ?string {
    return $this->status;
  }

  public function setStatus(string $status): static {
    $this->status = $status;

    return $this;
  }
}
