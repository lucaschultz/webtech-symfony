<?php

namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @template T of object
 * @template-extends ServiceEntityRepository<T>
 */
abstract class BaseRepository extends ServiceEntityRepository {
  protected TranslatorInterface $translator;

  /**
   * @param ManagerRegistry $registry
   * @param class-string<T> $entityClass
   * @param TranslatorInterface $translator
   */
  public function __construct(
    ManagerRegistry $registry,
    string $entityClass,
    TranslatorInterface $translator
  ) {
    parent::__construct($registry, $entityClass);
    $this->translator = $translator;
  }

  /**
   * Find an entity by ID or throw an exception if not found
   *
   * @param int $id The entity ID
   * @return T The entity
   * @throws NotFoundHttpException When entity is not found
   */
  public function findOrFail(int $id): object {
    $entity = $this->find($id);

    if (!$entity) {
      $entityName = (new \ReflectionClass(
        $this->getClassName()
      ))->getShortName();

      $message = $this->translator->trans("entity.not_found.by_id", [
        "%entity_name%" => $entityName,
        "%id%" => $id,
      ]);

      throw new NotFoundHttpException($message);
    }

    return $entity;
  }
}
