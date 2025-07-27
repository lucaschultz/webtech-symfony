<?php

namespace App\Controller;

use App\Constant\TaskFilterType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TaskController extends AbstractController {
  #[Route("/tasks", name: "app_tasks_list")]
  public function list(UserRepository $userRepository): Response {
    $user = $userRepository->findOrFail(1);
    $tasks = $user->getAssignedTasks();

    $this->addFlash("warning", "This action cannot be undone.");

    return $this->render("task/list.html.twig", [
      "tasks" => $tasks,
    ]);
  }

  #[Route("/tasks/{taskId}", name: "app_task_show")]
  public function show(): Response {
    return new Response("Hello");
  }

  #[Route("/tasks/{taskId}/status", name: "app_task_status", methods: ["POST"])]
  public function status(): Response {
    return new Response("Status update");
  }

  #[
    Route(
      "/tasks/{taskId}/priority",
      name: "app_task_priority",
      methods: ["POST"]
    )
  ]
  public function priority(): Response {
    return new Response("Priority update");
  }

  #[Route("/tasks/{taskId}/update", name: "app_task_update", methods: ["POST"])]
  public function update(): Response {
    return new Response("Task update");
  }

  #[Route("/tasks/{taskId}/delete", name: "app_task_delete", methods: ["POST"])]
  public function delete(): Response {
    return new Response("Task delete");
  }

  #[Route("/tasks/create", name: "app_task_create")]
  public function create(): Response {
    return new Response("Create task");
  }
}
