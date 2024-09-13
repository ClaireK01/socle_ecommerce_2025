<?php

namespace App\Repository;

use App\Entity\AttributeItem;
use App\Entity\AttributeItemTranslation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AttributeItemTranslation>
 *
 * @method AttributeItemTranslation|null find($id, $lockMode = null, $lockVersion = null)
 * @method AttributeItemTranslation|null findOneBy(array $criteria, array $orderBy = null)
 * @method AttributeItemTranslation[]    findAll()
 * @method AttributeItemTranslation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AttributeItemTranslationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AttributeItemTranslation::class);
    }

    public function add(AttributeItemTranslation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(AttributeItemTranslation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

}
