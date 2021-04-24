<?php

namespace App\Repository;

use App\Entity\ComicSeries;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @method ComicSeries|null find($id, $lockMode = null, $lockVersion = null)
 * @method ComicSeries|null findOneBy(array $criteria, array $orderBy = null)
 * @method ComicSeries[]    findAll()
 * @method ComicSeries[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ComicSeriesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ComicSeries::class);
    }


    public function findAllPaginated($currentPage = 1)
    {
        $query = $this->createQueryBuilder('c')
            ->getQuery();

        $paginator = $this->paginate($query, $currentPage);

        return $paginator;
    }

    public function paginate($dql, $page = 1, $limit = 6)
    {
        $paginator = new Paginator($dql);

        $paginator->getQuery()
            ->setFirstResult($limit * ($page - 1)) // Offset
            ->setMaxResults($limit); // Limit

        return $paginator;
    }

    /*
    public function findOneBySomeField($value): ?ComicSeries
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
