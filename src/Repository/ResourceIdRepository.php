<?php

namespace App\Repository;

use App\Entity\ResourceId;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ResourceId>
 *
 * @method ResourceId|null find($id, $lockMode = null, $lockVersion = null)
 * @method ResourceId|null findOneBy(array $criteria, array $orderBy = null)
 * @method ResourceId[]    findAll()
 * @method ResourceId[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ResourceIdRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ResourceId::class);
    }

//    /**
//     * @return ResourceId[] Returns an array of ResourceId objects
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

//    public function findOneBySomeField($value): ?ResourceId
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
