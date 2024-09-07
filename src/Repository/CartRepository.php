<?php

namespace App\Repository;

use App\Entity\Cart;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Cart>
 *
 * @method Cart|null find($id, $lockMode = null, $lockVersion = null)
 * @method Cart|null findOneBy(array $criteria, array $orderBy = null)
 * @method Cart[]    findAll()
 * @method Cart[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CartRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cart::class);
    }

    public function add(Cart $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Cart $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getExpiredCart($date){

        $qb = $this->createQueryBuilder('c');
        $qb
            ->where($qb->expr()->lte("c.expiration", ":date"))
            ->setParameter("date", $date);

        return $qb->getQuery()->getResult();
    }

    public function getActiveCartUser($user){
        $qb = $this->createQueryBuilder('c')
                    ->join('c.user_session', 's');

        $qb
            ->where($qb->expr()->eq('s.user', ':user'))
            ->setParameters(['user' => $user])
            ->orderBy('c.createdAt', 'DESC')
            ->setMaxResults(1)
        ;

        return $qb->getQuery()->getResult();
    }

}
