<?php

namespace App\Repository;

use App\Entity\Reclamation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ReclamationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reclamation::class);
    }

    public function findWithFilters(?string $search, ?string $statut, ?string $priorite, string $orderBy = 'dateReclamation', string $order = 'DESC'): array
    {
        $qb = $this->createQueryBuilder('r');
        
        if ($search) {
            $qb->andWhere('r.sujet LIKE :search OR r.description LIKE :search OR r.nomClient LIKE :search')
               ->setParameter('search', '%' . $search . '%');
        }
        
        if ($statut) {
            $qb->andWhere('r.statut = :statut')->setParameter('statut', $statut);
        }
        
        if ($priorite) {
            $qb->andWhere('r.priorite = :priorite')->setParameter('priorite', $priorite);
        }
        
        $qb->orderBy('r.' . $orderBy, $order);
        
        return $qb->getQuery()->getResult();
    }

    public function countByStatut(): array
    {
        return $this->createQueryBuilder('r')
            ->select('r.statut, COUNT(r.id) as total')
            ->groupBy('r.statut')
            ->getQuery()
            ->getResult();
    }

    public function countByPriorite(): array
    {
        return $this->createQueryBuilder('r')
            ->select('r.priorite, COUNT(r.id) as total')
            ->groupBy('r.priorite')
            ->getQuery()
            ->getResult();
    }

    public function findWithFiltersForUser(
    $user,
    ?string $search,
    ?string $statut,
    ?string $priorite,
    string $orderBy = 'dateReclamation',
    string $order = 'DESC'
): array {
    $qb = $this->createQueryBuilder('r')
        ->andWhere('r.user = :user')
        ->setParameter('user', $user);

    if ($search) {
        $qb->andWhere('r.sujet LIKE :search OR r.description LIKE :search OR r.nomClient LIKE :search')
           ->setParameter('search', '%' . $search . '%');
    }

    if ($statut) {
        $qb->andWhere('r.statut = :statut')
           ->setParameter('statut', $statut);
    }

    if ($priorite) {
        $qb->andWhere('r.priorite = :priorite')
           ->setParameter('priorite', $priorite);
    }

    $qb->orderBy('r.' . $orderBy, $order);

    return $qb->getQuery()->getResult();
}

}