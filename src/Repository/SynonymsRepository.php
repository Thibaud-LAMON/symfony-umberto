<?php

namespace App\Repository;

use App\Entity\Synonyms;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Synonyms>
 *
 * @method Synonyms|null find($id, $lockMode = null, $lockVersion = null)
 * @method Synonyms|null findOneBy(array $criteria, array $orderBy = null)
 * @method Synonyms[]    findAll()
 * @method Synonyms[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SynonymsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Synonyms::class);
    }

    public function save(Synonyms $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Synonyms $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function synonymExistsForIdea($synonym, $ideaId)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.synonym = :synonym')
            ->andWhere('s.ideas = :ideas_id')
            ->setParameter('synonym', $synonym)
            ->setParameter('ideas_id', $ideaId)
            ->getQuery()
            ->getOneOrNullResult() !== null;
    }

    public function findAllByIdea($ideaId)
    {
        return $this->createQueryBuilder('s')  // 's' est un alias pour 'synonyms'
            ->andWhere('s.ideas = :ideas_id')  // Filtre par l'idée spécifiée
            ->setParameter('ideas_id', $ideaId)  // Définit la valeur pour :ideaId
            ->getQuery()  // Obtient la requête
            ->getResult();  // Exécute la requête et obtient les résultats
    }

    //    /**
    //     * @return Synonyms[] Returns an array of Synonyms objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('s.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Synonyms
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
