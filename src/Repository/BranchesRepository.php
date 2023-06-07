<?php

namespace App\Repository;

use App\Entity\Branches;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Branches>
 *
 * @method Branches|null find($id, $lockMode = null, $lockVersion = null)
 * @method Branches|null findOneBy(array $criteria, array $orderBy = null)
 * @method Branches[]    findAll()
 * @method Branches[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BranchesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Branches::class);
    }

    public function save(Branches $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Branches $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findByUniverseId($universeId)
    {
        return $this->createQueryBuilder('b')
            ->where('b.universes = :universes_id')
            ->setParameter('universes_id', $universeId)
            ->getQuery()
            ->getResult();
    }

    public function findByProjectId($projectId)
    {
        return $this->createQueryBuilder('b')
            ->innerJoin('b.universes', 'u') // Rejoint sur Universes
            ->innerJoin('u.projects', 'p') // Rejoint sur Projects depuis Universes
            ->where('p.id = :projectId')
            ->setParameter('projectId', $projectId)
            ->getQuery()
            ->getResult();
    }
    //    /**
    //     * @return Branches[] Returns an array of Branches objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('b')
    //            ->andWhere('b.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('b.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Branches
    //    {
    //        return $this->createQueryBuilder('b')
    //            ->andWhere('b.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
