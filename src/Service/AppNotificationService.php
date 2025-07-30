<?php

namespace App\Service;

use App\Entity\AppNotification;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class AppNotificationService {
  private EntityManagerInterface $entityManager;
  private Security $security;

  public function __construct(
    EntityManagerInterface $entityManager,
    Security $security
  ) {
    $this->entityManager = $entityManager;
    $this->security = $security;
  }

  /**
   * Notify users
   * @param User[] $recipients Array of User entities to notify
   * @param string $message The notification message
   * @param string $link Link for the notification
   * @param User|null $exclude User to exclude from notifications, defaults to currently authenticated user
   */
  public function notifyUsers(
    array $recipients,
    string $message,
    string $link,
    ?User $exclude = null
  ): void {
    if ($exclude === null) {
      $exclude = $this->security->getUser();
    }

    foreach ($recipients as $recipient) {
      if ($exclude && $recipient === $exclude) {
        continue;
      }

      $notification = new AppNotification();
      $notification->setRecipient($recipient);
      $notification->setMessage($message);
      $notification->setLink($link);
      $notification->setIsRead(false);

      $this->entityManager->persist($notification);
    }

    $this->entityManager->flush();
  }
}
