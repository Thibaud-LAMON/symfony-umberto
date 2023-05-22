<?php

namespace App\Repository;

use App\Entity\Projects;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Projects>
 *
 * @method Projects|null find($id, $lockMode = null, $lockVersion = null)
 * @method Projects|null findOneBy(array $criteria, array $orderBy = null)
 * @method Projects[]    findAll()
 * @method Projects[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProjectsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Projects::class);
    }

    public function save(Projects $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Projects $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function countByUserId(int $userId): int
    {
        return $this->createQueryBuilder('p') //créer un objet QueryBuilder avec un alias pour l'entité Projects
            ->select('count(p.id)') //selectionne le nombre d'ID dans l'entité
            ->where('p.users = :users_id') //où les résultats de 'users' est égale au 'users_id' du projet
            ->setParameter('users_id', $userId) //la variable ':users_id' est la valeur de '$userId'
            ->getQuery() //convertit le QueryBuilder en une requête SQL
            ->getSingleScalarResult(); //éxecute la requête
    }

    public function findByUserId($userId)
    {
        return $this->createQueryBuilder('p') //créer un objet QueryBuilder avec un alias pour l'entité Projects
            ->where('p.users = :userId') //où les résultats de 'users' est égale au 'users_id' du projet
            ->setParameter('userId', $userId) //la variable ':users_id' est la valeur de '$userId'
            ->getQuery() //convertit le QueryBuilder en une requête SQL
            ->getResult(); //éxecute la requête
    }

    //    /**
    //     * @return Projects[] Returns an array of Projects objects
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

    //    public function findOneBySomeField($value): ?Projects
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
