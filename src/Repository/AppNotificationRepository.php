<?php

namespace App\Repository;

use App\Entity\AppNotification;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @extends BaseRepository<AppNotification>
 */
class AppNotificationRepository extends BaseRepository {
  public function __construct(
    ManagerRegistry $registry,
    TranslatorInterface $translator
  ) {
    parent::__construct($registry, AppNotification::class, $translator);
  }

  public function markAllAsReadForUser(User $user): void {
    $this->createQueryBuilder("n")
      ->update()
      ->set("n.isRead", ":isRead")
      ->where("n.recipient = :user")
      ->andWhere("n.isRead = :notRead")
      ->setParameter("isRead", true)
      ->setParameter("notRead", false)
      ->setParameter("user", $user)
      ->getQuery()
      ->execute();
  }

  public function deleteAllForUser(User $user): void {
    $this->createQueryBuilder("n")
      ->delete()
      ->where("n.recipient = :user")
      ->setParameter("user", $user)
      ->getQuery()
      ->execute();
  }

  //    /**
  //     * @return AppNotification[] Returns an array of AppNotification objects
  //     */
  //    public function findByExampleField($value): array
  //    {
  //        return $this->createQueryBuilder('a')
  //            ->andWhere('a.exampleField = :val')
  //            ->setParameter('val', $value)
  //            ->orderBy('a.id', 'ASC')
  //            ->setMaxResults(10)
  //            ->getQuery()
  //            ->getResult()
  //        ;
  //    }

  //    public function findOneBySomeField($value): ?AppNotification
  //    {
  //        return $this->createQueryBuilder('a')
  //            ->andWhere('a.exampleField = :val')
  //            ->setParameter('val', $value)
  //            ->getQuery()
  //            ->getOneOrNullResult()
  //        ;
  //    }
}
