<?php

namespace App\Repository;

use App\Entity\Shipping;
use App\Entity\ShippingTranslation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ShippingTranslation>
 *
 * @method ShippingTranslation|null find($id, $lockMode = null, $lockVersion = null)
 * @method ShippingTranslation|null findOneBy(array $criteria, array $orderBy = null)
 * @method ShippingTranslation[]    findAll()
 * @method ShippingTranslation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ShippingTranslationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ShippingTranslation::class);
    }

    public function add(ShippingTranslation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ShippingTranslation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

}
