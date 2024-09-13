<?php

namespace App\Repository;

use App\Entity\CardAttribute;
use App\Entity\CardAttributeTranslation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CardAttributeTranslation>
 *
 * @method CardAttributeTranslation|null find($id, $lockMode = null, $lockVersion = null)
 * @method CardAttributeTranslation|null findOneBy(array $criteria, array $orderBy = null)
 * @method CardAttributeTranslation[]    findAll()
 * @method CardAttributeTranslation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CardAttributeTranslationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CardAttributeTranslation::class);
    }

    public function add(CardAttributeTranslation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(CardAttributeTranslation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

}
