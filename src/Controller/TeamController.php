<?php

namespace App\Controller;

use App\Entity\Team;
use App\Form\TeamNewType;
use App\Repository\TeamRepository;
use App\Security\Voter\TeamVoter;
use App\Service\RedirectService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

final class TeamController extends AbstractController {
  #[Route("/teams/{id}", name: "app_team_show", requirements: ["id" => "\d+"])]
  public function show(Team $team): Response {
    try {
      $this->denyAccessUnlessGranted(TeamVoter::VIEW, $team);
    } catch (AccessDeniedException $e) {
      $this->addFlash("error", "You do not have permission to view this team.");
      return $this->redirectToRoute("app_teams_list");
    }

    return $this->render("team/show.html.twig", [
      "team" => $team,
      "TEAM_EDIT" => TeamVoter::EDIT,
      "TEAM_DELETE" => TeamVoter::DELETE,
      "TEAM_VIEW_REQUESTS" => TeamVoter::VIEW_REQUESTS,
    ]);
  }

  #[Route("/teams/new", name: "app_team_new")]
  public function new(
    Request $request,
    EntityManagerInterface $em,
    RedirectService $redirectService
  ): Response {
    try {
      $this->denyAccessUnlessGranted("IS_AUTHENTICATED_FULLY");
    } catch (AccessDeniedException $e) {
      $this->addFlash("error", "Please login or create a new account first.");
      return $this->redirectToRoute("app_home");
    }

    /** @var \App\Entity\User $user */
    $user = $this->getUser();
    $team = new Team();

    $form = $this->createForm(TeamNewType::class, $team, [
      "exclude_user" => $user,
    ]);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $team->setCreatedBy($user);
      $team->addUser($user);
      $em->persist($team);
      $em->flush();

      $this->addFlash("success", "Team created successfully!");

      return $redirectService->safeRedirect($request, "app_team_show", [
        "id" => $team->getId(),
      ]);
    }

    return $this->render("team/new.html.twig", [
      "form" => $form->createView(),
    ]);
  }

  #[Route("/teams/{id}/edit", name: "app_team_edit")]
  public function edit(
    Request $request,
    Team $team,
    EntityManagerInterface $em,
    RedirectService $redirectService
  ): Response {
    try {
      $this->denyAccessUnlessGranted(TeamVoter::EDIT, $team);
    } catch (AccessDeniedException $e) {
      $this->addFlash("error", "You do not have permission to edit this team.");
      return $this->redirectToRoute("app_team_show", ["id" => $team->getId()]);
    }

    $form = $this->createForm(TeamNewType::class, $team);

    $form = $this->createForm(TeamNewType::class, $team, [
      "exclude_user" => $team->getCreatedBy(),
    ]);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      if (!$team->getUsers()->contains($team->getCreatedBy())) {
        $team->addUser($team->getCreatedBy());
      }

      $em->flush();

      $this->addFlash("success", "Team updated successfully!");

      return $redirectService->safeRedirect($request, "app_team_show", [
        "id" => $team->getId(),
      ]);
    }

    return $this->render("team/edit.html.twig", [
      "form" => $form,
      "team" => $team,
    ]);
  }

  #[Route("/teams/{id}/delete", name: "app_team_delete", methods: ["POST"])]
  public function delete(
    Request $request,
    Team $team,
    EntityManagerInterface $em
  ): Response {
    try {
      $this->denyAccessUnlessGranted(TeamVoter::DELETE, $team);
    } catch (AccessDeniedException $e) {
      $this->addFlash(
        "error",
        "You do not have permission to delete this team."
      );
      return $this->redirectToRoute("app_team_show", ["id" => $team->getId()]);
    }

    $em->remove($team);
    $em->flush();

    $this->addFlash("success", "Team deleted successfully.");

    return $this->redirectToRoute("app_teams_list");
  }

  #[Route("/teams", name: "app_teams_list")]
  public function list(TeamRepository $teamRepository): Response {
    // Fetch all teams, with users, sorted by createdAt DESC
    $teams = $teamRepository
      ->createQueryBuilder("t")
      ->leftJoin("t.users", "u")
      ->addSelect("u")
      ->orderBy("t.createdAt", "DESC")
      ->getQuery()
      ->getResult();

    return $this->render("team/list.html.twig", [
      "teams" => $teams,
      "TEAM_VIEW" => TeamVoter::VIEW,
      "TEAM_EDIT" => TeamVoter::EDIT,
      "TEAM_DELETE" => TeamVoter::DELETE,
    ]);
  }

  #[Route("/teams/{id}/tasks", name: "app_team_tasks")]
  public function teamTasks(
    Team $team,
    Request $request,
    EntityManagerInterface $em
  ): Response {
    try {
      $this->denyAccessUnlessGranted(TeamVoter::VIEW, $team);
    } catch (AccessDeniedException $e) {
      $this->addFlash("error", "You do not have permission to view this team.");
      return $this->redirectToRoute("app_teams_list");
    }

    $sort = $request->query->get("sort", "createdAt");
    $direction = $request->query->get("direction", "desc");

    $allowedSortFields = [
      "title",
      "status",
      "priority",
      "deadline",
      "createdAt",
    ];
    if (!in_array($sort, $allowedSortFields)) {
      $sort = "createdAt";
    }

    if (!in_array($direction, ["asc", "desc"])) {
      $direction = "desc";
    }

    $tasks = $em
      ->getRepository("App\Entity\Task")
      ->createQueryBuilder("t")
      ->where("t.team = :team")
      ->setParameter("team", $team)
      ->leftJoin("t.createdBy", "cb")
      ->addSelect("cb")
      ->leftJoin("t.assignedTo", "at")
      ->addSelect("at")
      ->orderBy("t." . $sort, $direction)
      ->getQuery()
      ->getResult();

    return $this->render("team/tasks.html.twig", [
      "team" => $team,
      "tasks" => $tasks,
      "currentSort" => $sort,
      "currentDirection" => $direction,
      "TEAM_VIEW" => TeamVoter::VIEW,
      "TEAM_EDIT" => TeamVoter::EDIT,
      "TEAM_DELETE" => TeamVoter::DELETE,
    ]);
  }
}
