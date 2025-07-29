<?php

namespace App\Form;

use App\Entity\Team;
use App\Entity\User;
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
        "label" => "Add Members",
      ]);
  }

  public function configureOptions(OptionsResolver $resolver): void {
    $resolver->setDefaults([
      "data_class" => Team::class,
    ]);
  }
}
