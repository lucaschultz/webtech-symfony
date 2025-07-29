<?php

namespace App\Form;

use App\Entity\Team;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TeamNewType extends AbstractType {
  public function buildForm(
    FormBuilderInterface $builder,
    array $options
  ): void {
    $excludeUser = $options["exclude_user"];

    $builder
      ->add("name", TextType::class, [
        "label" => "Team Name",
        "attr" => [
          "placeholder" => "Enter tem title",
        ],
      ])
      ->add("description", TextareaType::class, [
        "label" => "Description",
        "required" => false,
        "attr" => [
          "placeholder" => "Enter team description",
          "rows" => 5,
        ],
      ])
      ->add("users", EntityType::class, [
        "class" => User::class,
        "choice_label" => function (User $user) {
          return $user->getFirstName() . " " . $user->getLastName();
        },
        "multiple" => true,
        "expanded" => true,
        "by_reference" => false,
        "required" => false,
        "query_builder" => function (UserRepository $ur) use ($excludeUser) {
          $qb = $ur->createQueryBuilder("u");
          if ($excludeUser) {
            $qb
              ->where("u != :excludeUser")
              ->setParameter("excludeUser", $excludeUser);
          }
          return $qb;
        },
      ]);
  }

  public function configureOptions(OptionsResolver $resolver): void {
    $resolver->setDefaults([
      "data_class" => Team::class,
      "exclude_user" => null,
    ]);
  }
}
