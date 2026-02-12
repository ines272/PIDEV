<?php

namespace App\Repository;

use App\Entity\Reponse;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Reponse>
 */
class ReponseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reponse::class);
    }

    /**
     * Récupérer toutes les réponses d'une réclamation
     */
    public function findByReclamationId(int $reclamationId): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.reclamation = :reclamationId')
            ->setParameter('reclamationId', $reclamationId)
            ->orderBy('r.dateReponse', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Compter le nombre de réponses par réclamation
     */
    public function countByReclamation(int $reclamationId): int
    {
        return $this->createQueryBuilder('r')
            ->select('COUNT(r.id)')
            ->andWhere('r.reclamation = :reclamationId')
            ->setParameter('reclamationId', $reclamationId)
            ->getQuery()
            ->getSingleScalarResult();
    }
}