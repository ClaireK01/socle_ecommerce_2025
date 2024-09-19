<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\VarDumper\VarDumper;
use function Doctrine\ORM\QueryBuilder;

/**
 * @extends ServiceEntityRepository<Product>
 *
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function add(Product $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Product $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function filterProducts($params){
        $qb = $this->createQueryBuilder('p')
                    ->leftJoin('p.attributeItems', 'a')
                    ->leftJoin('a.productAttribute', 'pa');

        $attrs = array_filter($params, function($e, $key){
            return str_contains($key, "attr_");
        }, ARRAY_FILTER_USE_BOTH);

        $qb = $this->filterPrice($qb, $params, 'p');

        if($this->existAndNotEmpty("category", $params)){
            $qb->andWhere(":category MEMBER OF p.categories");
            $qb->setParameter('category',  $params['category']);
        }

        $attrs = array_filter($params, function($e, $key){
            return str_contains($key, "attr_");
        }, ARRAY_FILTER_USE_BOTH);

        $ind = 0;
        foreach($attrs as $i => $pa){
            $arr = [];
            $arr[$i] = $pa;
            $ind++;
            $qb->andWhere($qb->expr()->in('p.id', $this->attrSubQuery($qb, $params, $arr, $ind)));
        }

        return $qb->getQuery()->getResult();
    }


    public function attrSubQuery($qb, $params, $productAttributes, $index)
    {
        $productAlias = "p".$index;
        $attributeAlias = "a".$index;
        $productAttributeAlias = "pa".$index;

        $subQuery = $this->createQueryBuilder($productAlias)
            ->select($productAlias.".id")
            ->leftJoin($productAlias.".attributeItems", $attributeAlias)
            ->leftJoin($attributeAlias . '.productAttribute', $productAttributeAlias);

        $subQuery = $this->filterPrice($subQuery, $params, $productAlias);

        if ($this->existAndNotEmpty("category", $params)) {
            $subQuery->andWhere(":category MEMBER OF ". $productAlias .".categories");
            $qb->setParameter('category', $params['category']);
        }

        $orX = $subQuery->expr()->orX();

        foreach ($productAttributes as $i => $a) {
            $idPa = str_replace('attr_', '', $i);
            $arr = is_array($a) ? $a : [$a];
            foreach ($arr as $item) {
                $orX->add(
                    $subQuery->expr()->andX(
                        $subQuery->expr()->eq($productAttributeAlias.'.id', ':pa'. $index .'_' . $idPa),
                        $subQuery->expr()->eq($attributeAlias.'.id', ':a'. $index .'_' . $item),
                    )
                );
                $qb->setParameter(':pa'. $index .'_' . $idPa, $idPa);
                $qb->setParameter(':a'. $index .'_' . $item, $item);
            }
        }

        $subQuery->andWhere($orX);

        return $subQuery->getQuery()->getDQL();
    }

    public function filterPrice($qb, $params, $alias){
        if ($this->existAndNotEmpty("price_min", $params) || $this->existAndNotEmpty("price_max", $params)) {
            if ($this->existAndNotEmpty("price_min", $params) && $this->existAndNotEmpty("price_max", $params)) {
                $qb->andWhere($qb->expr()->between( $alias.'.price', ':price_min', ':price_max'));
                $qb->setParameter('price_max', $params['price_max']);
                $qb->setParameter('price_min', $params['price_min']);
            } else if ($this->existAndNotEmpty("price_min", $params)) {
                $qb->andWhere($qb->expr()->gte( $alias.'.price', ':price_min'));
                $qb->setParameter('price_min', $params['price_min']);
            } else if ($this->existAndNotEmpty("price_max", $params)) {
                $qb->andWhere($qb->expr()->lte( $alias.'.price', ':price_max'));
                $qb->setParameter('price_max', $params['price_max']);
            }
        }

        return $qb;
    }

    public function existAndNotEmpty($key, $array)
    {
        return array_key_exists( $key, $array) && $array[$key] !== null && $array[$key] !== "" ;
    }

}
