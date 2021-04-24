<?php

namespace App\Repository;

use App\Entity\NESRom;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @method NESRom|null find($id, $lockMode = null, $lockVersion = null)
 * @method NESRom|null findOneBy(array $criteria, array $orderBy = null)
 * @method NESRom[]    findAll()
 * @method NESRom[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NESRomRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NESRom::class);
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

    // /**
    //  * @return NESRom[] Returns an array of NESRom objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('n.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?NESRom
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
