<?php

namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

final class TeamVoter extends Voter {
  public const VIEW = "TEAM_VIEW";
  public const EDIT = "TEAM_EDIT";
  public const DELETE = "TEAM_DELETE";

  protected function supports(string $attribute, mixed $subject): bool {
    return in_array($attribute, [self::VIEW, self::EDIT, self::DELETE]) &&
      $subject instanceof \App\Entity\Team;
  }

  protected function voteOnAttribute(
    string $attribute,
    mixed $subject,
    TokenInterface $token
  ): bool {
    $user = $token->getUser();

    if (!$user instanceof User) {
      return false;
    }

    /** @var Team $team */
    $team = $subject;

    switch ($attribute) {
      case self::VIEW:
        return $team->hasMember($user);
      case self::EDIT:
        return $team->getCreatedBy() === $user;
      case self::DELETE:
        return $team->getCreatedBy() === $user;
    }

    return false;
  }
}
