<?php

namespace App\Controller;

use App\Repository\AppNotificationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AppNotificationController extends AbstractController {
  public function __construct(
    private AppNotificationRepository $notificationRepository,
    private EntityManagerInterface $entityManager
  ) {
  }

  #[Route("/notifications", name: "app_notifications_list")]
  public function index(): Response {
    $notifications = $this->notificationRepository->findBy(
      ["recipient" => $this->getUser()],
      ["createdAt" => "DESC"]
    );

    return $this->render("app_notification/list.html.twig", [
      "notifications" => $notifications,
    ]);
  }

  #[
    Route(
      "/app/notification/mark-all-read",
      name: "app_notification_mark_all_read",
      methods: ["POST"]
    )
  ]
  public function markAllAsRead(): Response {
    $this->notificationRepository->markAllAsReadForUser($this->getUser());

    $this->addFlash("success", "All notifications marked as read.");

    return $this->redirectToRoute("app_notifications_list");
  }

  #[
    Route(
      "/app/notification/delete-all",
      name: "app_notification_delete_all",
      methods: ["POST"]
    )
  ]
  public function deleteAll(): Response {
    $this->notificationRepository->deleteAllForUser($this->getUser());

    $this->addFlash("success", "All notifications deleted.");

    return $this->redirectToRoute("app_notifications_list");
  }

  #[
    Route(
      "/app/notification/{id}/read",
      name: "app_notification_mark_read",
      methods: ["POST"]
    )
  ]
  public function markAsRead(int $id): Response {
    $notification = $this->notificationRepository->findOneBy([
      "id" => $id,
      "recipient" => $this->getUser(),
    ]);

    if ($notification && !$notification->isRead()) {
      $notification->setIsRead(true);
      $this->entityManager->flush();
    }

    if ($notification?->getLink()) {
      return $this->redirect($notification->getLink());
    }

    return $this->redirectToRoute("app_notifications_list");
  }
}
