<?php

namespace App\Form;

use App\Constant\TaskPriority;
use App\Constant\TaskStatus;
use App\Entity\Task;
use App\Entity\Team;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class TaskNewType extends AbstractType {
  public function buildForm(
    FormBuilderInterface $builder,
    array $options
  ): void {
    $builder
      ->add("title", TextType::class, [
        "label" => "Task Title",
        "attr" => [
          "placeholder" => "Enter task title",
        ],
        "constraints" => [
          new Assert\NotBlank(message: "Task title is required"),
          new Assert\Length(
            max: 255,
            maxMessage: "Task title cannot be longer than {{ limit }} characters"
          ),
        ],
      ])
      ->add("description", TextareaType::class, [
        "label" => "Description",
        "required" => false,
        "attr" => [
          "placeholder" => "Enter task description",
          "rows" => 5,
        ],
      ])
      ->add("team", EntityType::class, [
        "class" => Team::class,
        "label" => "Team",
        "choice_label" => "name",
        "placeholder" => "Select a team",
      ])
      ->add("status", EnumType::class, [
        "class" => TaskStatus::class,
        "label" => "Status",
        "choice_label" => function (TaskStatus $status) {
          return match ($status) {
            TaskStatus::Todo => "To Do",
            TaskStatus::InProgress => "In Progress",
            TaskStatus::Done => "Done",
          };
        },
        "constraints" => [new Assert\NotNull(message: "Status is required")],
      ])
      ->add("priority", EnumType::class, [
        "class" => TaskPriority::class,
        "label" => "Priority",
        "choice_label" => function (TaskPriority $priority) {
          return match ($priority) {
            TaskPriority::High => "High",
            TaskPriority::Medium => "Medium",
            TaskPriority::Low => "Low",
          };
        },
        "constraints" => [new Assert\NotNull(message: "Priority is required")],
      ])
      ->add("deadline", DateTimeType::class, [
        "label" => "Deadline",
        "required" => false,
        "widget" => "single_text",
        "html5" => true,
        "constraints" => [
          new Assert\GreaterThanOrEqual(
            value: "today",
            message: "Deadline cannot be in the past"
          ),
          new Assert\LessThanOrEqual(
            value: "+10 years",
            message: "Deadline cannot be more than 10 years in the future"
          ),
        ],
      ])
      ->add("assignedTo", EntityType::class, [
        "class" => User::class,
        "label" => "Assigned To",
        "required" => false,
        "choice_label" => function (User $user) {
          return $user->getFirstName() . " " . $user->getLastName();
        },
        "placeholder" => "Select a user",
      ]);
  }

  public function configureOptions(OptionsResolver $resolver): void {
    $resolver->setDefaults([
      "data_class" => Task::class,
    ]);
  }
}
