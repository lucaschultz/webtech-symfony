<?php

namespace App\Controller;

use App\Entity\Team;
use App\Form\TeamNewType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

final class TeamController extends AbstractController {
  #[Route("/teams/{id}", name: "app_team_show", requirements: ["id" => "\d+"])]
  public function show(Team $team): Response {
    $this->denyAccessUnlessGranted("IS_AUTHENTICATED_FULLY");
    // ToDo: add logic to restrict viewing to team members only!

    return $this->render("team/show.html.twig", [
      "team" => $team,
    ]);
  }

  #[Route("/teams/new", name: "app_team_new")]
  public function new(Request $request, EntityManagerInterface $em): Response {
    $this->denyAccessUnlessGranted("IS_AUTHENTICATED_FULLY"); // Only logged-in users

    $team = new Team();
    $form = $this->createForm(TeamNewType::class, $team, [
      "exclude_user" => $this->getUser(),
    ]);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      // Add current user as owner and as member
      $team->setCreatedBy($this->getUser());
      $team->addUser($this->getUser());
      $em->persist($team);
      $em->flush();

      $this->addFlash("success", "Team created successfully!");

      return $this->redirectToRoute("app_team_show", ["id" => $team->getId()]);
    }

    return $this->render("team/new.html.twig", [
      "form" => $form->createView(),
    ]);
  }

  #[Route("/team/{id}/edit", name: "app_team_edit")]
  public function edit(
    Request $request,
    Team $team,
    EntityManagerInterface $em
  ): Response {
    $this->denyAccessUnlessGranted("IS_AUTHENTICATED_FULLY");

    $form = $this->createForm(TeamNewType::class, $team);

    // Exclude owner from selectable members
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

      return $this->redirectToRoute("app_team_show", ["id" => $team->getId()]);
    }

    return $this->render("team/edit.html.twig", [
      "form" => $form,
      "team" => $team,
    ]);
  }
}
