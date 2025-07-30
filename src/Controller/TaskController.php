<?php

namespace App\Controller;

use App\Constant\TaskPriority;
use App\Constant\TaskStatus;
use App\Entity\Comment;
use App\Entity\Task;
use App\Entity\Team;
use App\Form\CommentType;
use App\Form\TaskEditType;
use App\Form\TaskNewType;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use App\Service\AppNotificationService;
use App\Service\RedirectService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TaskController extends AbstractController {
  private AppNotificationService $notificationService;

  public function __construct(AppNotificationService $notificationService) {
    $this->notificationService = $notificationService;
  }

  #[Route("/tasks", name: "app_tasks_list")]
  public function list(
    Request $request,
    TaskRepository $taskRepository
  ): Response {
    $sortBy = $request->query->get("sort", "title");
    $sortDirection = $request->query->get("direction", "asc");

    $allowedSortFields = [
      "title",
      "status",
      "priority",
      "deadline",
      "createdAt",
    ];

    if (!in_array($sortBy, $allowedSortFields)) {
      $sortBy = "title";
    }

    if (!in_array($sortDirection, ["asc", "desc"])) {
      $sortDirection = "asc";
    }

    $tasks = $taskRepository
      ->createQueryBuilder("t")
      ->where("t.assignedTo = :user")
      ->orWhere("t.createdBy = :user")
      ->setParameter("user", $this->getUser())
      ->orderBy("t.$sortBy", $sortDirection)
      ->getQuery()
      ->getResult();

    return $this->render("task/list.html.twig", [
      "tasks" => $tasks,
      "currentSort" => $sortBy,
      "currentDirection" => $sortDirection,
    ]);
  }

  #[
    Route(
      "/tasks/{taskId}",
      name: "app_task_show",
      methods: ["GET", "POST"],
      requirements: ["taskId" => "\d+"]
    )
  ]
  public function show(
    int $taskId,
    Request $request,
    EntityManagerInterface $em,
    TaskRepository $taskRepository
  ): Response {
    $task = $taskRepository->findOrFail($taskId);

    $comment = new Comment();
    $commentForm = $this->createForm(CommentType::class, $comment);
    $commentForm->handleRequest($request);
    if ($commentForm->isSubmitted() && $commentForm->isValid()) {
      $comment->setAuthor($this->getUser());
      $comment->setTask($task);

      $em->persist($comment);
      $em->flush();

      /** @var \App\Entity\User $user */
      $user = $this->getUser();

      $this->notificationService->notifyUsers(
        $task->getStakeholders(),
        'New comment on "' . $task->getTitle() . '" by ' . $user->getFullName(),
        $this->generateUrl("app_task_show", ["taskId" => $task->getId()]) .
          "#comment-" .
          $comment->getId()
      );

      $this->addFlash("success", "Comment added successfully!");

      return $this->redirect(
        $this->generateUrl("app_task_show", ["taskId" => $task->getId()]) .
          "#comment-" .
          $comment->getId()
      );
    }

    return $this->render("task/show.html.twig", [
      "task" => $task,
      "commentForm" => $commentForm->createView(),
    ]);
  }

  #[
    Route(
      "/tasks/{taskId}/status/{status}",
      name: "app_task_status",
      methods: ["POST"]
    )
  ]
  public function updateStatus(
    Request $request,
    int $taskId,
    string $status,
    TaskRepository $taskRepository,
    EntityManagerInterface $entityManager,
    RedirectService $redirectService
  ): Response {
    $task = $taskRepository->findOrFail($taskId);

    try {
      $taskStatus = TaskStatus::from($status);
      $task->setStatus($taskStatus);
      $entityManager->flush();

      $this->addFlash("success", "Task status updated successfully");

      /** @var \App\Entity\User $user */
      $user = $this->getUser();

      $this->notificationService->notifyUsers(
        $task->getStakeholders(),
        'Status of task "' .
          $task->getTitle() .
          '" updated by ' .
          $user->getFullName(),
        $this->generateUrl("app_task_show", ["taskId" => $task->getId()])
      );
    } catch (\ValueError $e) {
      $this->addFlash("error", "Invalid status value");
    }

    return $redirectService->safeRedirect($request);
  }

  #[
    Route(
      "/tasks/{taskId}/priority/{priority}",
      name: "app_task_priority",
      methods: ["POST"]
    )
  ]
  public function updatePriority(
    Request $request,
    int $taskId,
    string $priority,
    TaskRepository $taskRepository,
    EntityManagerInterface $entityManager,
    RedirectService $redirectService
  ): Response {
    $task = $taskRepository->findOrFail($taskId);

    try {
      $taskPriority = TaskPriority::from($priority);
      $task->setPriority($taskPriority);
      $entityManager->flush();

      $this->addFlash("success", "Task priority updated successfully");

      /** @var \App\Entity\User $user */
      $user = $this->getUser();

      $this->notificationService->notifyUsers(
        $task->getStakeholders(),
        'Priority of task "' .
          $task->getTitle() .
          '" updated by ' .
          $user->getFullName(),
        $this->generateUrl("app_task_show", ["taskId" => $task->getId()])
      );
    } catch (\ValueError $e) {
      $this->addFlash("error", "Invalid priority value");
    }

    return $redirectService->safeRedirect($request);
  }

  #[
    Route(
      "/tasks/{taskId}/edit",
      name: "app_task_update",
      methods: ["GET", "POST"]
    )
  ]
  public function update(
    Request $request,
    int $taskId,
    TaskRepository $taskRepository,
    EntityManagerInterface $entityManager,
    RedirectService $redirectService
  ): Response {
    $task = $taskRepository->findOrFail($taskId);

    $form = $this->createForm(TaskEditType::class, $task);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $entityManager->flush();

      $this->addFlash("success", "Task updated successfully");

      /** @var \App\Entity\User $user */
      $user = $this->getUser();

      $this->notificationService->notifyUsers(
        $task->getStakeholders(),
        'Task "' . $task->getTitle() . '" updated by ' . $user->getFullName(),
        $this->generateUrl("app_task_show", ["taskId" => $task->getId()])
      );

      return $redirectService->safeRedirect($request, "app_task_show", [
        "taskId" => $task->getId(),
      ]);
    }

    return $this->render("task/edit.html.twig", [
      "task" => $task,
      "form" => $form,
    ]);
  }

  #[Route("/tasks/{taskId}/delete", name: "app_task_delete", methods: ["POST"])]
  public function delete(
    Request $request,
    int $taskId,
    TaskRepository $taskRepository,
    EntityManagerInterface $entityManager,
    RedirectService $redirectService
  ): Response {
    $task = $taskRepository->findOrFail($taskId);
    $taskTitle = $task->getTitle();

    $entityManager->remove($task);
    $entityManager->flush();

    /** @var \App\Entity\User $user */
    $user = $this->getUser();

    $this->notificationService->notifyUsers(
      $task->getStakeholders(),
      "Task \"{$taskTitle}\" deleted by " . $user->getFullName(),
      $this->generateUrl("app_tasks_list")
    );

    $this->addFlash("success", "Task deleted successfully");

    return $redirectService->safeRedirect($request, "app_tasks_list");
  }

  #[Route("/tasks/new", name: "app_task_new", methods: ["GET", "POST"])]
  public function new(
    Request $request,
    EntityManagerInterface $entityManager,
    RedirectService $redirectService,
    UserRepository $userRepository
  ): Response {
    $task = new Task();

    $task->setCreatedBy($this->getUser());

    if ($request->query->has("title")) {
      $task->setTitle($request->query->get("title"));
    }

    if ($request->query->has("description")) {
      $task->setDescription($request->query->get("description"));
    }

    if ($request->query->has("team")) {
      $teamId = $request->query->getInt("team");
      $team = $entityManager->getRepository(Team::class)->find($teamId);
      if ($team) {
        $task->setTeam($team);
      }
    }

    if ($request->query->has("assignedTo")) {
      $userId = $request->query->getInt("assignedTo");
      $user = $userRepository->find($userId);
      if ($user) {
        $task->setAssignedTo($user);
      }
    }

    if ($request->query->has("status")) {
      try {
        $status = TaskStatus::from($request->query->get("status"));
        $task->setStatus($status);
      } catch (\ValueError) {
        $task->setStatus(TaskStatus::Todo);
      }
    } else {
      $task->setStatus(TaskStatus::Todo);
    }

    if ($request->query->has("priority")) {
      try {
        $priority = TaskPriority::from($request->query->get("priority"));
        $task->setPriority($priority);
      } catch (\ValueError) {
        $task->setPriority(TaskPriority::Medium);
      }
    } else {
      $task->setPriority(TaskPriority::Medium);
    }

    if ($request->query->has("deadline")) {
      try {
        $deadline = new \DateTime($request->query->get("deadline"));
        $task->setDeadline($deadline);
      } catch (\Exception) {
        // Invalid date format, ignore
      }
    }

    $form = $this->createForm(TaskNewType::class, $task);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $entityManager->persist($task);
      $entityManager->flush();

      $this->addFlash("success", "Task created successfully");

      /** @var \App\Entity\User $user */
      $user = $this->getUser();

      $this->notificationService->notifyUsers(
        $task->getStakeholders(),
        'New task "' .
          $task->getTitle() .
          '" created by ' .
          $user->getFullName(),
        $this->generateUrl("app_task_show", ["taskId" => $task->getId()])
      );

      return $redirectService->safeRedirect($request, "app_task_show", [
        "taskId" => $task->getId(),
      ]);
    }

    return $this->render("task/new.html.twig", [
      "form" => $form,
    ]);
  }

  #[
    Route(
      "/tasks/{taskId}/assign/{userId}",
      name: "app_task_assign",
      methods: ["POST"],
      requirements: ["taskId" => "\d+", "userId" => "\d+"]
    )
  ]
  public function assignTask(
    Request $request,
    int $taskId,
    int $userId,
    TaskRepository $taskRepository,
    UserRepository $userRepository,
    EntityManagerInterface $entityManager,
    RedirectService $redirectService
  ): Response {
    $task = $taskRepository->findOrFail($taskId);

    if ($userId === 0) {
      $task->setAssignedTo(null);
      $entityManager->flush();
      $this->addFlash("success", "Task unassigned successfully");
      return $redirectService->safeRedirect($request);
    }

    $user = $userRepository->find($userId);

    if (!$user) {
      $this->addFlash("error", "User not found");
      return $redirectService->safeRedirect($request);
    }

    if (
      !$task
        ->getTeam()
        ->getUsers()
        ->contains($user)
    ) {
      $this->addFlash("error", "User is not a member of this team");
      return $redirectService->safeRedirect($request);
    }

    $task->setAssignedTo($user);
    $entityManager->flush();

    $this->addFlash(
      "success",
      sprintf("Task assigned to %s successfully", $user->getFullName())
    );

    /** @var \App\Entity\User $user */
    $user = $this->getUser();

    $this->notificationService->notifyUsers(
      $task->getStakeholders(),
      'Assignment of task "' .
        $task->getTitle() .
        '" updated by ' .
        $user->getFullName(),
      $this->generateUrl("app_task_show", ["taskId" => $task->getId()])
    );

    return $redirectService->safeRedirect($request);
  }
}
