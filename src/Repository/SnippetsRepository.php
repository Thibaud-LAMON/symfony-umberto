<?php

namespace App\Repository;

use App\Entity\Snippets;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Users;

/**
 * @extends ServiceEntityRepository<Snippets>
 *
 * @method Snippets|null find($id, $lockMode = null, $lockVersion = null)
 * @method Snippets|null findOneBy(array $criteria, array $orderBy = null)
 * @method Snippets[]    findAll()
 * @method Snippets[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SnippetsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Snippets::class);
    }

    public function save(Snippets $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Snippets $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function countBySnippet(int $userId): int
    {
        $qb = $this->createQueryBuilder('s'); //créer un objet QueryBuilder avec un alias pour l'entité Snippets

        return $qb->select('count(s.snippet)') //selectionne le nombre de snippet dans l'entité
            ->join('App\Entity\Ideas', 'i', 'WITH', 's MEMBER OF i.snippets') //Jointure avec l'entité Ideas (i) en utilisant la condition "s MEMBER OF i.snippets" qui vérifie si l'entité Snippets possède la clé secondaire ideas_id.
            ->join('App\Entity\Branches', 'b', 'WITH', 'i MEMBER OF b.ideas') //Jointure avec l'entité Branches (b) Ideas est liée à Branches par branches_id.
            ->join('App\Entity\Universes', 'u', 'WITH', 'b MEMBER OF u.branches') //Jointure avec l'entité Universes (u) Branches est liée à Universes par universes_id.
            ->join('App\Entity\Projects', 'p', 'WITH', 'u MEMBER OF p.universes') //Jointure avec l'entité Projects (p) Universes est liée à Projects par projects_id.
            ->where('p.users = :users_id') //où les résultats de 'users' est égale au 'users_id' du projet
            ->setParameter('users_id', $userId) //la variable ':users_id' est la valeur de '$userId'
            ->getQuery() //convertit le QueryBuilder en une requête SQL
            ->getSingleScalarResult(); //éxecute la requête
    }

    public function countByTrunc(int $userId): int
    {
        $qb = $this->createQueryBuilder('s'); //créer un objet QueryBuilder avec un alias pour l'entité Snippets

        return $qb->select('count(s.truncated)') //selectionne le nombre de snippets tronqués dans l'entité
            ->join('App\Entity\Ideas', 'i', 'WITH', 's MEMBER OF i.snippets') //Jointure avec l'entité Ideas (i) en utilisant la condition "s MEMBER OF i.snippets" qui vérifie si l'entité Snippets possède la clé secondaire ideas_id.
            ->join('App\Entity\Branches', 'b', 'WITH', 'i MEMBER OF b.ideas') //Jointure avec l'entité Branches (b) Ideas est liée à Branches par branches_id.
            ->join('App\Entity\Universes', 'u', 'WITH', 'b MEMBER OF u.branches') //Jointure avec l'entité Universes (u) Branches est liée à Universes par universes_id.
            ->join('App\Entity\Projects', 'p', 'WITH', 'u MEMBER OF p.universes') //Jointure avec l'entité Projects (p) Universes est liée à Projects par projects_id.
            ->where('p.users = :users_id') //où les résultats de 'users' est égale au 'users_id' du projet
            ->setParameter('users_id', $userId) //la variable ':users_id' est la valeur de '$userId'
            ->getQuery() //convertit le QueryBuilder en une requête SQL
            ->getSingleScalarResult(); //éxecute la requête
    }

    public function snippetExistsForIdea($snippetText, $ideaId)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.snippet = :snippet')
            ->andWhere('s.ideas = :ideas_id')
            ->setParameter('snippet', $snippetText)
            ->setParameter('ideas_id', $ideaId)
            ->getQuery()
            ->getOneOrNullResult() !== null;
    }

    //    /**
    //     * @return Snippets[] Returns an array of Snippets objects
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

    //    public function findOneBySomeField($value): ?Snippets
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
