<?php

namespace App\Repository;

use App\Entity\Category;
use App\Entity\CategoryTranslation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CategoryTranslation>
 *
 * @method CategoryTranslation|null find($id, $lockMode = null, $lockVersion = null)
 * @method CategoryTranslation|null findOneBy(array $criteria, array $orderBy = null)
 * @method CategoryTranslation[]    findAll()
 * @method CategoryTranslation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryTranslationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CategoryTranslation::class);
    }

    public function add(CategoryTranslation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(CategoryTranslation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

}
