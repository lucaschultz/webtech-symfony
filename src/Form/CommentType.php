<?php

namespace App\Form;

use App\Entity\Comment;
use App\Entity\Task;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommentType extends AbstractType {
  public function buildForm(
    FormBuilderInterface $builder,
    array $options
  ): void {
    $builder->add("content", TextareaType::class, [
      "label" => "New Comment",
      "attr" => [
        "rows" => 3,
        "placeholder" => "Type your comment here...",
      ],
    ]);
  }

  public function configureOptions(OptionsResolver $resolver): void {
    $resolver->setDefaults([
      "data_class" => Comment::class,
    ]);
  }
}
