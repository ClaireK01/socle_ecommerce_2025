<?php

namespace App\Twig;

use App\Entity\Cart;
use App\Entity\Category;
use App\Entity\Conversation;
use App\Entity\EcommerceOrder;
use App\Entity\Product;
use App\Repository\CartRepository;
use App\Repository\ConversationRepository;
use App\Service\OrderService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\VarDumper\VarDumper;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use Twig\TwigTest;

class TwigExtension extends AbstractExtension
{

    public function __construct(    private readonly ConversationRepository $conversationRepository, private readonly OrderService $orderService,
                                    private readonly CartRepository $cartRepository, private readonly TranslatorInterface $translator,
                                    private readonly EntityManagerInterface $entityManager, private readonly RouterInterface $router,
                                    private readonly ParameterBagInterface $parameterBag
    )
    {

    }

    public function getFilters()
    {
        return [
            new TwigFilter('svg', [$this, 'svg']),
        ];
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('getLastMessage', [$this, 'getLastMessage']),
            new TwigFunction('getShopCount', [$this, 'getShopCount']),
            new TwigFunction('getQuantityProductCart', [$this, 'getQuantityProductCart']),
            new TwigFunction('getRoute', [$this, 'getRoute']),
            new TwigFunction('vdump', [$this, 'vdump']),
            new TwigFunction('die', [$this, 'die']),
            new TwigFunction('generateBreadcrumb', [$this, 'generateBreadcrumb']),
            new TwigFunction('hasBreadcrumb', [$this, 'hasBreadcrumb']),
            new TwigFunction('getCategories', [$this, 'getCategories']),
        ];
    }

    public function getTests()
    {
        return [
            new TwigTest("order", [$this, "isOrder"]),
        ];

    }

    public function getLastMessage($user, $member)
    {
        /** @var Conversation $conversation */
        $conversations = $this->conversationRepository->getConversationByMember($user, $member);
        $conversation = null;
        if(!empty($conversations)){
            $conversation = $conversations[0];
        }

        if($conversation){
            return $conversation->getMessages()->last();
        }

        return null;
    }

    public function getShopCount($session){
        $cart = $this->cartRepository->findOneBy(['session_id' => $session->getId()]);

        if($cart instanceof Cart){
            return $this->orderService->getCartInfos($cart)['total_quantity'] ?? 0;
        }

        return 0;
    }


    public function isOrder($var)
    {
        return $var instanceof EcommerceOrder;
    }

    //////////////////////////////////////////////////////// breadcrumb //////////////////////////////////////////////

    public function generateBreadcrumb(Request $request, $entity = null){
        $finalBc = '';
        $finalBc = $this->getCrumb($finalBc, $request->get("_route"), $request->getLocale(), $entity);

        return $finalBc;
    }

    public function getCrumb($string, $routeName, $locale, $entity){

        $breadcrumb = '';
        $route = $this->router->getRouteCollection()->get($routeName);
        if($route){
            $params = $route->getOption("breadcrumb");
            $routeParameters = [];
            if($entity != null){
                $routeParameters = ["id" => $entity->getId()];
            }
            $link = $this->router->generate($routeName, $routeParameters);

            if($params['name'] == "%name%" && $entity){
                $crumb = $entity->translate($locale)->getTitle();
            }else{
                $crumb = $this->translator->trans("breadcrumb.".$params['name'], [], "messages", $locale);
            }
            $breadcrumb = '<li class="breadcrumb-item"><a href="'. $link.'">' . $crumb . '</a></li>' . $string ;

            if(key_exists('parent', $params) && $params['parent'] != null){
                return $this->getCrumb($breadcrumb, $params['parent'], $locale, $this->getParentEntity($entity));
            }
        }


         return $breadcrumb;
    }

    public function getParentEntity($entity)
    {
        if($entity instanceof Product){
            return $entity->getFirstCategory();
        }
        if($entity instanceof Category){
            return $entity->getParent();
        }
        return null;
    }

    public function hasBreadcrumb($name){
        return $this->router->getRouteCollection()->get($name)->getOption('breadcrumb');
    }

    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function getCategories(){
        return $this->entityManager->getRepository(Category::class)->findBy(['deletedAt' => null]);
    }

    public function svg(string $path)
    {
        $str = "";
        $dir = $this->parameterBag->get("kernel.project_dir"). '/public/svg/'. $path;
        if(file_exists($dir)){
            $str = file_get_contents($dir);
        }
        return $str;
    }

    public function getQuantityProductCart($cart, $product){
        if($cart){
            foreach ($cart->getCardProducts() as $c){
                if($c->getProduct() == $product){
                    return $c->getQuantity();
                }
            }
        }

        return 1;
    }

    public function getRoute($route, $params = []){
        try{
            return $this->router->generate($route, $params);
        } catch(\Exception $e){
            return null;
        }
    }

    public function vdump($var){
        VarDumper::dump($var);
    }

    public function die(){
        die;
    }

}