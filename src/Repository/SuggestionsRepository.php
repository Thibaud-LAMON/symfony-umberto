<?php

namespace App\Repository;

use App\Entity\Suggestions;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Suggestions>
 *
 * @method Suggestions|null find($id, $lockMode = null, $lockVersion = null)
 * @method Suggestions|null findOneBy(array $criteria, array $orderBy = null)
 * @method Suggestions[]    findAll()
 * @method Suggestions[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SuggestionsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Suggestions::class);
    }

    public function save(Suggestions $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Suggestions $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function countBySuggestion(int $userId): int
    {
        $qb = $this->createQueryBuilder('s'); //créer un objet QueryBuilder avec un alias pour l'entité Snippets

        return $qb->select('count(s.suggestion)') //selectionne le nombre de suggestions dans l'entité
            ->join('App\Entity\Ideas', 'i', 'WITH', 's MEMBER OF i.suggestions') //Jointure avec l'entité Ideas (i) Suggestion est liée à Ideas via la clé secondaire ideas_id.
            ->join('App\Entity\Branches', 'b', 'WITH', 'i MEMBER OF b.ideas') //Jointure avec l'entité Branches (b) Ideas est liée à Branches par branches_id.
            ->join('App\Entity\Universes', 'u', 'WITH', 'b MEMBER OF u.branches') //Jointure avec l'entité Universes (u) Branches est liée à Universes par universes_id.
            ->join('App\Entity\Projects', 'p', 'WITH', 'u MEMBER OF p.universes') //Jointure avec l'entité Projects (p) Universes est liée à Projects par projects_id.
            ->where('p.users = :users_id') //où les résultats de 'users' est égale au 'users_id' du projet
            ->setParameter('users_id', $userId) //la variable ':users_id' est la valeur de '$userId'
            ->getQuery() //convertit le QueryBuilder en une requête SQL
            ->getSingleScalarResult(); //éxecute la requête
    }

    public function suggestionExistsForIdea($suggestion, $ideaId)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.suggestion = :suggestion')
            ->andWhere('s.ideas = :ideas_id')
            ->setParameter('suggestion', $suggestion)
            ->setParameter('ideas_id', $ideaId)
            ->getQuery()
            ->getOneOrNullResult() !== null;
    }

    //    /**
    //     * @return Suggestions[] Returns an array of Suggestions objects
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

    //    public function findOneBySomeField($value): ?Suggestions
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
