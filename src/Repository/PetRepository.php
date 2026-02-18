<?php

namespace App\Repository;

use App\Entity\Pet;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Enum\PetType;
use App\Enum\Gender;


/**
 * @extends ServiceEntityRepository<Pet>
 */
class PetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Pet::class);
    }


    public function searchByCriteria(
    ?string $name,
    ?PetType $type,
    ?Gender $gender,
    ?bool $vaccinated,
    ?bool $critical,
    ?User $owner = null
): array {
    $qb = $this->createQueryBuilder('p');

    if ($name) {
        $qb->andWhere('p.name LIKE :name')
           ->setParameter('name', '%' . $name . '%');
    }

    if ($type) {
        $qb->andWhere('p.typePet = :type')
           ->setParameter('type', $type);
    }

    if ($gender) {
        $qb->andWhere('p.gender = :gender')
           ->setParameter('gender', $gender);
    }

    if ($vaccinated !== null) {
        $qb->andWhere('p.isVacinated = :vaccinated')
           ->setParameter('vaccinated', $vaccinated);
    }

    if ($critical !== null) {
        $qb->andWhere('p.hasCriticalCondition = :critical')
           ->setParameter('critical', $critical);
    }

    return $qb->getQuery()->getResult();
}

    //    /**
    //     * @return Pet[] Returns an array of Pet objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Pet
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
