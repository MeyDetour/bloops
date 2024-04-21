<?php

namespace App\Repository;

use App\Entity\Bloop;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Bloop>
 *
 * @method Bloop|null find($id, $lockMode = null, $lockVersion = null)
 * @method Bloop|null findOneBy(array $criteria, array $orderBy = null)
 * @method Bloop[]    findAll()
 * @method Bloop[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BloopRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Bloop::class);
    }

//    /**
//     * @return Bloop[] Returns an array of Bloop objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Bloop
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
