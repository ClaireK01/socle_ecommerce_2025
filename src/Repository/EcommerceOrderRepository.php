<?php

namespace App\Repository;

use App\Entity\EcommerceOrder;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EcommerceOrder>
 *
 * @method EcommerceOrder|null find($id, $lockMode = null, $lockVersion = null)
 * @method EcommerceOrder|null findOneBy(array $criteria, array $orderBy = null)
 * @method EcommerceOrder[]    findAll()
 * @method EcommerceOrder[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EcommerceOrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EcommerceOrder::class);
    }

    public function add(EcommerceOrder $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(EcommerceOrder $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getLastOrderNumber()
    {
        $qb = $this->createQueryBuilder('o');
        $qb->orderBy('o.createdAt', 'DESC')
            ->setMaxResults(1);

        $order = $qb->getQuery()->getOneOrNullResult();

        return $order ? $order->getNumber() : 0;
    }

}
