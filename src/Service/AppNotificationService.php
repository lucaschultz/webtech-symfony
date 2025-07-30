<?php

namespace App\Service;

use App\Entity\AppNotification;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class AppNotificationService {
  private EntityManagerInterface $entityManager;

  public function __construct(EntityManagerInterface $entityManager) {
    $this->entityManager = $entityManager;
  }

  /**
   * Notify all users in array (except $exclude)
   */
  public function notifyUsers(
    array $recipients,
    string $message,
    ?string $link = null,
    ?User $exclude = null
  ): void {
    foreach ($recipients as $recipient) {
      if ($exclude && $recipient === $exclude) {
        continue;
      }
      $notification = new AppNotification();
      $notification->setRecipient($recipient);
      $notification->setMessage($message);
      $notification->setLink($link ?? "");
      $notification->setIsRead(false);

      $this->entityManager->persist($notification);
    }
    $this->entityManager->flush();
  }
}
