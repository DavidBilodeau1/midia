<?php

namespace App\Repository;

use App\Entity\ComicHistory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ComicHistory|null find($id, $lockMode = null, $lockVersion = null)
 * @method ComicHistory|null findOneBy(array $criteria, array $orderBy = null)
 * @method ComicHistory[]    findAll()
 * @method ComicHistory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ComicHistoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ComicHistory::class);
    }

    public function findByUser($userId)
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.user = :val')
            ->setParameter('val', $userId)
            ->join('h.comic', 'c')
            ->orderBy('c.series', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    // /**
    //  * @return ComicHistory[] Returns an array of ComicHistory objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ComicHistory
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
