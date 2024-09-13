<?php

namespace App\Repository;

use App\Entity\ProductAttribute;
use App\Entity\ProductAttributeTranslation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ProductAttributeTranslation>
 *
 * @method ProductAttributeTranslation|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProductAttributeTranslation|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProductAttributeTranslation[]    findAll()
 * @method ProductAttributeTranslation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductAttributeTranslationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProductAttributeTranslation::class);
    }

    public function add(ProductAttributeTranslation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ProductAttributeTranslation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

}
