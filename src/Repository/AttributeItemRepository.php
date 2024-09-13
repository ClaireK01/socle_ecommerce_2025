<?php

namespace App\Repository;

use App\Entity\AttributeItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AttributeItem>
 *
 * @method AttributeItem|null find($id, $lockMode = null, $lockVersion = null)
 * @method AttributeItem|null findOneBy(array $criteria, array $orderBy = null)
 * @method AttributeItem[]    findAll()
 * @method AttributeItem[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AttributeItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AttributeItem::class);
    }

    public function add(AttributeItem $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(AttributeItem $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

}
