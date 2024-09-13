<?php

namespace App\Repository;

use App\Entity\CardAttribute;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CardAttribute>
 *
 * @method CardAttribute|null find($id, $lockMode = null, $lockVersion = null)
 * @method CardAttribute|null findOneBy(array $criteria, array $orderBy = null)
 * @method CardAttribute[]    findAll()
 * @method CardAttribute[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CardAttributeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CardAttribute::class);
    }

    public function add(CardAttribute $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(CardAttribute $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

}
