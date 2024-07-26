<?php

namespace App\Repository;

use App\Entity\Tva;
use App\Entity\TvaTranslation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TvaTranslation>
 *
 * @method TvaTranslation|null find($id, $lockMode = null, $lockVersion = null)
 * @method TvaTranslation|null findOneBy(array $criteria, array $orderBy = null)
 * @method TvaTranslation[]    findAll()
 * @method TvaTranslation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TvaTranslationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TvaTranslation::class);
    }

    public function add(TvaTranslation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(TvaTranslation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

}
