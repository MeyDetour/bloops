<?php

namespace App\Repository;

use App\Entity\FriendRequest;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FriendRequest>
 *
 * @method FriendRequest|null find($id, $lockMode = null, $lockVersion = null)
 * @method FriendRequest|null findOneBy(array $criteria, array $orderBy = null)
 * @method FriendRequest[]    findAll()
 * @method FriendRequest[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FriendRequestRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FriendRequest::class);
    }
    public function findRequestsCreatedBetweenDates(\DateTimeImmutable $startDate, \DateTimeImmutable $endDate, User $user): int
    {
        $qb = $this->createQueryBuilder('r');

        // Construisez la requête
        $qb->select('COUNT(r.id)')  // Comptez le nombre d'utilisateurs
        ->where('r.createdAt >= :startDate') // Filtrez par la date de début
        ->andWhere('r.createdAt <= :endDate') // Filtrez par la date de fin
        ->andWhere('r.requested = :user') // Filtrez par la date de fin
        ->andWhere('r.requester = :user') // Filtrez par la date de fin
        ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->setParameter('user', $user);

        // Exécutez la requête et récupérez la valeur unique
        $query = $qb->getQuery();
        return $query->getSingleScalarResult();
    }
    //    /**
    //     * @return FriendRequest[] Returns an array of FriendRequest objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('f')
    //            ->andWhere('f.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('f.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?FriendRequest
    //    {
    //        return $this->createQueryBuilder('f')
    //            ->andWhere('f.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
