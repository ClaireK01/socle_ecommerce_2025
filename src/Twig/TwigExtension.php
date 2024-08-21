<?php

namespace App\Twig;

use App\Entity\Cart;
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
            new TwigFilter('header', [$this, 'header']),
            new TwigFilter('list', [$this, 'list']),
            new TwigFilter('svg', [$this, 'svg']),
        ];
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('getLastMessage', [$this, 'getLastMessage']),
            new TwigFunction('getShopCount', [$this, 'getShopCount']),
            new TwigFunction('getAdminTable', [$this, 'getAdminTable']),
            new TwigFunction('getQuantityProductCart', [$this, 'getQuantityProductCart']),
            new TwigFunction('getRoute', [$this, 'getRoute']),
            new TwigFunction('vdump', [$this, 'vdump']),
            new TwigFunction('die', [$this, 'die']),
            new TwigFunction('generateBreadcrumb', [$this, 'generateBreadcrumb']),
            new TwigFunction('hasBreadcrumb', [$this, 'hasBreadcrumb']),
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


    //////////////////////////////////////////////////////// admin //////////////////////////////////////////////


    public function header($header){

        $string = '';
        foreach ($header as $i => $h){
            $title = $this->translator->trans("header" . $this->getHeaderValue($h), [], 'admin');
            $string .= "<th>" . $title ."</th>";
        }

        $string .= "<th>" . $this->translator->trans('header.actions', [], 'admin') ."</th>";
        return $string;
    }

    public function getHeaderValue($value, $string = null){
        if(is_array($value)){
            $string = '.' . array_key_first($value) ;
            return  $string . $this->getHeaderValue($value[array_key_first($value)], $string);
        }else{
            return $string ? str_replace('.' . $value, '', $string) : '.' . $value;
        }
    }


    public function list($header, $class, $pagination, $request){

        $string = '';
        $reflection = new \ReflectionClass($class);
        try{
            $translationReflection = new \ReflectionClass($class . "Translation");
        }catch (\Exception $e){
            $translationReflection = null;
        }
        
        foreach ($pagination as $entity){
            
            $row = '<tr>';
            foreach ($header as $val) {
                $test = is_array($val) ? array_key_first($val) : $val;
                if($translationReflection){
                    if(!in_array($test, ['title', 'content', 'shortContent'])){
                        $method = $reflection->getMethod('get' . ucfirst($test));
                        $line =  $method->invoke($entity);
                    }else{
                        $method = $translationReflection->getMethod('get' . ucfirst($test));
                        $line =  $method->invoke($entity->translate($request->getLocale()));
                    }
                }else{
                    $line = $this->getLine($reflection, $val, $entity);
                }
                $row .= '<td>'. $line . '</td>';
            }

            $pathShow = $this->getRoute(str_replace('index', 'show', $request->get('_route')), ['id' => $entity->getId()]);
            $pathEdit = $this->getRoute(str_replace('index', 'edit', $request->get('_route')), ['id' => $entity->getId()]);
            $pathDelete = $this->getRoute(str_replace('index', 'delete', $request->get('_route')), ['id' => $entity->getId()]);



            $row .= "<td>";
            $row .= "<a href='". $pathShow."'><button class='btn-action'>". $this->translator->trans('action.show', [], 'admin') ."</button></a>";
            if($pathEdit){
                $row .= "<a href='". $pathEdit."'><button class='btn-action'>". $this->translator->trans('action.edit', [], 'admin') ."</button></a>";
            }
            if($pathDelete) {
                $row.=  "<a href='". $pathDelete."'><button class='btn-action'>". $this->translator->trans('action.delete', [], 'admin') ."</button></a>";
            }
            $row .= "</td>";
            $row .= "</tr>";

            $string.= $row;
        }

        return $string;
    }

    public function getLine($reflection, $value, $entity)
    {
        if(is_array($value)){
            $key = array_key_first($value);
            $method = $reflection->getMethod('get' . ucfirst($key));
            $e = $method->invoke($entity);
            if(is_object($e) && $class  = get_class($e)){
                $reflection = new \ReflectionClass($class);
                $value = is_array($value) ? $value[$key] : $value;
                return $this->getLine($reflection, $value, $e);
            }else{
                return $e;
            }
        }else{
            $method = $reflection->getMethod('get' . ucfirst($value));
            $line =  $method->invoke($entity);
        }
        
        return $line;
    }

    public function getAdminTable($entity, $request)
    {

        $class = get_class($entity);
        $array = (array) $entity;
        $table = [];

        foreach($array as $i=>$a){
            $key = str_replace([$class, '*'], '',strip_tags($i));
            if(is_string($a) || is_null($a)){
                $table[$key] = $a;
            }else{
                if($key == 'translations'){
                    $table[$this->translator->trans('header.title', [], 'admin')] = $entity->translate($request->getLocale())->getTitle();
                    $table[$this->translator->trans('header.shortContent', [], 'admin')] = $entity->translate($request->getLocale())->getShortContent();
                    $table[$this->translator->trans('header.content', [], 'admin')] = $entity->translate($request->getLocale())->getContent();
                }
            }
        }

        unset($table['currentLocale']);
        unset($table['defaultLocale']);
        unset($table['newTranslations']);

        return $table;
    }

    public function getRoute($route, $params = []){
        try{
            return $this->router->generate($route, $params);
        } catch(\Exception $e){
            return null;
        }
    }

    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////
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
        return null;
    }

    public function hasBreadcrumb($name){
        return $this->router->getRouteCollection()->get($name)->getOption('breadcrumb');
    }

    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////

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

    public function vdump($var){
        VarDumper::dump($var);
    }

    public function die(){
        die;
    }

}