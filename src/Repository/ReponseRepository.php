<?php

namespace App\Repository;

use App\Entity\Reponse;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Reponse>
 *
 * @method Reponse|null find($id, $lockMode = null, $lockVersion = null)
 * @method Reponse|null findOneBy(array $criteria, array $orderBy = null)
 * @method Reponse[]    findAll()
 * @method Reponse[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReponseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reponse::class);
    }

    public function add(Reponse $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Reponse $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findAllById($id)
    {
        return $this->createQueryBuilder('reponse')
        ->innerJoin('reponse.question', 'q')
        ->where('q.id = :id')
        ->setParameter('id', $id)
        ->getQuery()
        ->getResult();
    }

    public function expected($id)
    {
        return $this->createQueryBuilder('reponse')
        ->innerJoin('reponse.question', 'q')
        ->where('q.categorie = :id and reponse.reponse_expected = 1')
        ->setParameter('id', $id)
        ->getQuery()
        ->getResult();
    }

    public function findReponseExpected($id)
    {
        return $this->createQueryBuilder('reponse')
        ->innerJoin('reponse.question', 'q')
        ->where('q.id = :id and reponse.reponse_expected = 1')
        ->setParameter('id', $id)
        ->getQuery()
        ->getResult();
    }

//    /**
//     * @return Reponse[] Returns an array of Reponse objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('r.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Reponse
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
