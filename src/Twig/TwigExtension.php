<?php

namespace App\Twig;

use App\Entity\Cart;
use App\Entity\Conversation;
use App\Entity\EcommerceOrder;
use App\Repository\CartRepository;
use App\Repository\ConversationRepository;
use App\Service\OrderService;
use Doctrine\ORM\EntityManagerInterface;
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
                                    private readonly EntityManagerInterface $entityManager, private readonly RouterInterface $router
    )
    {

    }

    public function getFilters()
    {
        return [
            new TwigFilter('header', [$this, 'header']),
            new TwigFilter('list', [$this, 'list']),
        ];
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('getLastMessage', [$this, 'getLastMessage']),
            new TwigFunction('getShopCount', [$this, 'getShopCount']),
            new TwigFunction('getAdminTable', [$this, 'getAdminTable']),
            new TwigFunction('vdump', [$this, 'vdump']),
            new TwigFunction('die', [$this, 'die']),
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

    public function header($header){

        $string = '';
        foreach ($header as $h){
            $title = $this->translator->trans('header.' . $h, [], 'admin');
            $string .= "<th>" . $title ."</th>";
        }

        $string .= "<th>" . $this->translator->trans('header.actions', [], 'admin') ."</th>";
        return $string;
    }


    public function list($header, $class, $pagination, $request){

        $string = '';
        $reflection = new \ReflectionClass($class);
        try{
            $translationReflection = new \ReflectionClass($class . "Translation");
        }catch (\Exception $e){
            $translationReflection = null;
        }
        
        foreach ($pagination as $e){
            
            $row = '<tr>';
            foreach ($header as $h) {
                if($translationReflection){
                    if(!in_array($h, ['title', 'content', 'shortContent'])){
                        $method = $reflection->getMethod('get' . ucfirst($h));
                        $line =  $method->invoke($e);
                    }else{
                        $method = $translationReflection->getMethod('get' . ucfirst($h));
                        $line =  $method->invoke($e->translate($request->getLocale()));
                    }
                }else{
                    $method = $reflection->getMethod('get' . ucfirst($h));
                    $line =  $method->invoke($e);
                }
                $row .= '<td>'. $line . '</td>';
            }

            $pathShow = $this->router->generate(str_replace('index', 'show', $request->get('_route')), ['id' => $e->getId()]);
            $pathEdit = $this->router->generate(str_replace('index', 'edit', $request->get('_route')), ['id' => $e->getId()]);
            $pathDelete = $this->router->generate(str_replace('index', 'delete', $request->get('_route')), ['id' => $e->getId()]);

            $row .= "<td>";
            $row .= "<a href='". $pathShow."'><button class='btn-action'>". $this->translator->trans('action.show', [], 'admin') ."</button></a>";
            $row .= "<a href='". $pathEdit."'><button class='btn-action'>". $this->translator->trans('action.edit', [], 'admin') ."</button></a>";
            $row.=  "<a href='". $pathDelete."'><button class='btn-action'>". $this->translator->trans('action.delete', [], 'admin') ."</button></a>";
            $row .= "</td>";
            $row .= "</tr>";

            $string.= $row;
        }

        return $string;
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


        public function vdump($var){
            VarDumper::dump($var);
        }

        public function die(){
        die;
        }
    }