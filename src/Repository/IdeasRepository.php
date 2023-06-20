<?php

namespace App\Repository;

use App\Entity\Ideas;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Ideas>
 *
 * @method Ideas|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ideas|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ideas[]    findAll()
 * @method Ideas[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class IdeasRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ideas::class);
    }

    public function save(Ideas $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Ideas $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findByBranchId($branchId)
    {
        return $this->createQueryBuilder('i')
            ->where('i.branches = :branches_id')
            ->setParameter('branches_id', $branchId)
            ->getQuery()
            ->getResult();
    }

    public function findByUniverseId($universeId)
    {
        return $this->createQueryBuilder('i')
            ->innerJoin('i.ideas', 'b') // Rejoint sur Branches
            ->innerJoin('b.universes', 'u') // Rejoint sur Universes depuis Branches
            ->where('u.id = :universeId')
            ->setParameter('universeId', $universeId)
            ->getQuery()
            ->getResult();
    }

    public function findByProjectId($projectId)
    {
        return $this->createQueryBuilder('i')
            ->innerJoin('i.ideas', 'b') // Rejoint sur Branches
            ->innerJoin('b.universes', 'u') // Rejoint sur Universes depuis Branches
            ->innerJoin('u.projects', 'p') // Rejoint sur Projects depuis Universes
            ->where('p.id = :projectId')
            ->setParameter('projectId', $projectId)
            ->getQuery()
            ->getResult();
    }

    public function findIdeaById($ideaId)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.id = :ideaId')
            ->setParameter('ideaId', $ideaId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    //    /**
    //     * @return Ideas[] Returns an array of Ideas objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('i')
    //            ->andWhere('i.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('i.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Ideas
    //    {
    //        return $this->createQueryBuilder('i')
    //            ->andWhere('i.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
