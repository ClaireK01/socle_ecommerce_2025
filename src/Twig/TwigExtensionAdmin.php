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

class TwigExtensionAdmin extends AbstractExtension
{

    public function __construct(    private readonly ConversationRepository $conversationRepository, private readonly OrderService $orderService,
                                    private readonly CartRepository $cartRepository, private readonly TranslatorInterface $translator,
                                    private readonly EntityManagerInterface $entityManager, private readonly RouterInterface $router,
                                    private readonly ParameterBagInterface $parameterBag
    )
    {}

    public function getFilters()
    {
        return [
            new TwigFilter('header', [$this, 'header']),
            new TwigFilter('list', [$this, 'list']),
            new TwigFunction('getRoute', [$this, 'getRoute']),
        ];
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('getAdminTable', [$this, 'getAdminTable']),
        ];
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

}