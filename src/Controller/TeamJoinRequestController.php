<?php

namespace App\Controller;

use App\Entity\Team;
use App\Entity\TeamJoinRequest;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class TeamJoinRequestController extends AbstractController {
  //   #[Route("/team/join-request", name: "app_team_join_request")]
  //   public function index(): Response {
  //     return $this->render("team_join_request/index.html.twig", [
  //       "controller_name" => "TeamJoinRequestController",
  //     ]);
  //   }

  #[
    Route(
      "/teams/{id}/join-request",
      name: "app_team_join_request",
      methods: ["POST"]
    )
  ]
  public function requestJoin(
    Team $team,
    EntityManagerInterface $em
  ): Response {
    $user = $this->getUser();

    // Prevent duplicate requests
    foreach ($team->getTeamJoinRequests() as $request) {
      if (
        $request->getRequester() === $user &&
        $request->getStatus() === "PENDING"
      ) {
        $this->addFlash(
          "info",
          "You have already requested to join this team."
        );
        return $this->redirectToRoute("app_teams_list");
      }
    }

    // Prevent if already a member
    if ($team->hasMember($user)) {
      $this->addFlash("info", "You are already a member of this team.");
      return $this->redirectToRoute("app_teams_list");
    }

    $joinRequest = new TeamJoinRequest();
    $joinRequest->setTeam($team);
    $joinRequest->setRequester($user);
    $joinRequest->setStatus("PENDING");

    $em->persist($joinRequest);
    $em->flush();

    $this->addFlash(
      "success",
      "Request sent! The team owner will review your request."
    );

    return $this->redirectToRoute("app_teams_list");
  }

  #[
    Route(
      "/join-request/{id}/accept",
      name: "app_join_request_accept",
      methods: ["POST"]
    )
  ]
  public function accept(
    TeamJoinRequest $joinRequest,
    EntityManagerInterface $em
  ): Response {
    $user = $this->getUser();
    $team = $joinRequest->getTeam();

    // Only team creator can accept
    if ($team->getCreatedBy() !== $user) {
      throw $this->createAccessDeniedException();
    }

    // Accept the request
    $joinRequest->setStatus(TeamJoinRequest::STATUS_ACCEPTED);
    $team->addUser($joinRequest->getRequester());
    $em->flush();

    $this->addFlash("success", "Request accepted and member added!");
    return $this->redirectToRoute("app_team_show", ["id" => $team->getId()]);
  }

  #[
    Route(
      "/join-request/{id}/decline",
      name: "app_join_request_decline",
      methods: ["POST"]
    )
  ]
  public function decline(
    TeamJoinRequest $joinRequest,
    EntityManagerInterface $em
  ): Response {
    $user = $this->getUser();
    $team = $joinRequest->getTeam();

    // Only team creator can decline
    if ($team->getCreatedBy() !== $user) {
      throw $this->createAccessDeniedException();
    }

    $joinRequest->setStatus(TeamJoinRequest::STATUS_DECLINED);
    $em->flush();

    $this->addFlash("info", "Request declined.");
    return $this->redirectToRoute("app_team_show", ["id" => $team->getId()]);
  }
}
