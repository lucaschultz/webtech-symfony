<?php

namespace App\Repository;

use App\Entity\Task;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @extends BaseRepository<Task>
 */
class TaskRepository extends BaseRepository {
  public function __construct(
    ManagerRegistry $registry,
    TranslatorInterface $translator
  ) {
    parent::__construct($registry, Task::class, $translator);
  }

  //    /**
  //     * @return Task[] Returns an array of Task objects
  //     */
  //    public function findByExampleField($value): array
  //    {
  //        return $this->createQueryBuilder('t')
  //            ->andWhere('t.exampleField = :val')
  //            ->setParameter('val', $value)
  //            ->orderBy('t.id', 'ASC')
  //            ->setMaxResults(10)
  //            ->getQuery()
  //            ->getResult()
  //        ;
  //    }

  //    public function findOneBySomeField($value): ?Task
  //    {
  //        return $this->createQueryBuilder('t')
  //            ->andWhere('t.exampleField = :val')
  //            ->setParameter('val', $value)
  //            ->getQuery()
  //            ->getOneOrNullResult()
  //        ;
  //    }
}
