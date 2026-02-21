<?php

namespace App\Repository;

use App\Entity\Event;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Event>
 */
class EventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }


    public function searchByCriteriaForUser(
    $user,
    ?string $name,
    ?\DateTime $date,
    ?string $heure
): array {
    $qb = $this->createQueryBuilder('e')
        ->andWhere('e.user = :user')
        ->setParameter('user', $user);

    if ($name) {
        $qb->andWhere('e.name LIKE :name')
           ->setParameter('name', '%' . $name . '%');
    }

    if ($date) {
        $qb->andWhere('e.date = :date')
           ->setParameter('date', $date);
    }

    if ($heure) {
        $qb->andWhere('e.heure LIKE :heure')
           ->setParameter('heure', '%' . $heure . '%');
    }

    return $qb->orderBy('e.date', 'DESC')
              ->getQuery()
              ->getResult();
}



//    /**
//     * @return Event[] Returns an array of Event objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('e.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Event
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
