<?php


namespace App\Service;

use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\VarDumper\VarDumper;

class RouterService
{

    public function __construct(
        private readonly RouterInterface $router, private readonly ArticleRepository $articleRepository, private readonly ProductRepository $productRepository,
        private readonly CategoryRepository $categoryRepository, private readonly ParameterBagInterface $parameterBag
    )
    {}

    public function sitemap(){
        $routes = [];
        
        foreach ($this->router->getRouteCollection() as $routeName => $route){
            if($route->getOption('sitemap')){
                foreach(explode('|', $this->parameterBag->get('locales')) as $locale){

                    if($routeName == "app_article_show"){
                        foreach ($this->articleRepository->findAll() as $article){
                            $routes[] = $this->router->generate($routeName, ['id' => $article->getId(), '_locale' => $locale]);
                        }
                    }else if($routeName == "app_product_show"){
                        foreach ($this->productRepository->findAll() as $product){
                            $routes[] = $this->router->generate($routeName, ['id' => $product->getId(), '_locale' => $locale]);
                        }
                    }else if($routeName == "app_product_list"){
                        foreach ($this->categoryRepository->findAll() as $category){
                            $routes[] = $this->router->generate($routeName, ['id' => $category->getId(), '_locale' => $locale]);
                        }
                    }else{
                        $routes[] = $this->router->generate($routeName, [ '_locale' => $locale]);
                    }
                }

            }
        }
        
        return $routes;

    }
}