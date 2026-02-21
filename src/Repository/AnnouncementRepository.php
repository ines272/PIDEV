<?php

namespace App\Repository;

use App\Entity\Announcement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Announcement>
 */
class AnnouncementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Announcement::class);
    }

    //    /**
    //     * @return Announcement[] Returns an array of Announcement objects
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

    //    public function findOneBySomeField($value): ?Announcement
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
     public function search(array $filters): array
    {
        $qb = $this->createQueryBuilder('a');

        // Filtrer par date début
        if (!empty($filters['dateDebut'])) {
            $qb->andWhere('a.dateDebut >= :dateDebut')
               ->setParameter('dateDebut', new \DateTime($filters['dateDebut']));
        }

        // Filtrer par date fin
        if (!empty($filters['dateFin'])) {
            $qb->andWhere('a.dateFin <= :dateFin')
               ->setParameter('dateFin', new \DateTime($filters['dateFin']));
        }

        // Filtrer par visites par jour
        if (!empty($filters['visitPerDay'])) {
            $qb->andWhere('a.visitPerDay = :visitPerDay')
               ->setParameter('visitPerDay', $filters['visitPerDay']);
        }

        // Filtrer par prix minimum
        if (!empty($filters['minPrice'])) {
            $qb->andWhere('a.renumerationMin >= :minPrice')
               ->setParameter('minPrice', $filters['minPrice']);
        }

        // Filtrer par prix maximum
        if (!empty($filters['maxPrice'])) {
            $qb->andWhere('a.renumerationMax <= :maxPrice')
               ->setParameter('maxPrice', $filters['maxPrice']);
        }

        return $qb->orderBy('a.dateDebut', 'DESC')
                  ->getQuery()
                  ->getResult();
    }


    public function searchByCriteria(
    ?string $address,
    ?string $dateDebut,
    ?string $dateFin
): array
{
    $qb = $this->createQueryBuilder('a');

    if ($address) {
        $qb->andWhere('a.address LIKE :address')
           ->setParameter('address', '%' . $address . '%');
    }

    if ($dateDebut) {
        $qb->andWhere('a.dateDebut >= :dateDebut')
           ->setParameter('dateDebut', new \DateTime($dateDebut));
    }

    if ($dateFin) {
        $qb->andWhere('a.dateFin <= :dateFin')
           ->setParameter('dateFin', new \DateTime($dateFin));
    }

    return $qb->getQuery()->getResult();
}

public function searchByCriteriaForUser(
    $user,
    ?string $address,
    ?string $dateDebut,
    ?string $dateFin
): array {

    $qb = $this->createQueryBuilder('a')
        ->andWhere('a.user = :user')
        ->setParameter('user', $user);

    if ($address) {
        $qb->andWhere('a.address LIKE :address')
           ->setParameter('address', '%' . $address . '%');
    }

    if ($dateDebut) {
        $qb->andWhere('a.dateDebut >= :dateDebut')
           ->setParameter('dateDebut', new \DateTime($dateDebut));
    }

    if ($dateFin) {
        $qb->andWhere('a.dateFin <= :dateFin')
           ->setParameter('dateFin', new \DateTime($dateFin));
    }

    return $qb->getQuery()->getResult();
}
}
