<?php


namespace App\Service;

use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\VarDumper\VarDumper;

class AdminService
{

    public function __construct(){}

    public function filter($qb, $filter, $entityAlias, $translationAlias = null){

        if($filter) {
            $params = [];

            foreach ($filter as $key => $val) {
                if (!is_null($val) && $val != "") {
                    $alias = $entityAlias;
                    if (in_array($key, ['title', 'content', 'shortContent'])) {
                        if ($translationAlias) {
                            $alias = $translationAlias;
                        }
                        $qb->andWhere($qb->expr()->like($alias . '.' . $key, ':'.$key.'value'));
                        $params[':'.$key.'value'] = "%" . $val . "%";
                    } else {
                        if(in_array($key, ['email'])){
                            $alias = 'u';
                            $expr = $qb->expr()->like($alias . '.' . $key, ':'.$key.'value');
                            $params[':'.$key.'value'] = "%" . $val . "%";

                        }elseif (in_array($key, ['firstName', 'lastName'])){
                            $alias = 's';
                            $expr = $qb->expr()->like($alias . '.' . $key, ':'.$key.'value');
                            $params[':'.$key.'value'] = "%" . $val . "%";

                        }else{
                            $expr = $qb->expr()->eq($alias . '.' . $key, ':'.$key.'value');
                            $params[':'.$key.'value'] = $val;
                        }
                        $qb->andWhere($expr);
                    }
                }
            }

            $qb->setParameters($params);
        }
        
//        VarDumper::dump($qb->getParameters());
//        die(VarDumper::dump($qb->getQuery()->getDQL()));

        return $qb;
    }
}