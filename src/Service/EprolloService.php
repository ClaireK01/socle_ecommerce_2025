<?php


namespace App\Service;


use App\Entity\Address;
use App\Entity\AttributeItem;
use App\Entity\CardAttribute;
use App\Entity\CardAttributeTranslation;
use App\Entity\CardProduct;
use App\Entity\Cart;
use App\Entity\Constant;
use App\Entity\EcommerceOrder;
use App\Entity\User;
use App\Entity\UserSession;
use App\Repository\AttributeItemRepository;
use App\Repository\CartRepository;
use App\Repository\EcommerceOrderRepository;
use Doctrine\DBAL\Driver\Exception;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\VarDumper\VarDumper;

class EprolloService
{

    public const PUBLIC_KEY = "9f533c17e9050f9c2336a9a5b740";
    public const SECRET_KEY = "a6801ae4daa5e8747a3846d0ed6069c3340a69111bb8";

    private Client $client;

    public function __construct(private readonly EntityManagerInterface $entityManager, private readonly LoggerInterface $logger)
    {
        $this->client = new Client();
    }


    public function getProductList(){
        return $this->sendData('product_list', 'GET', []);
    }

    public function addOrder(EcommerceOrder $order) : EcommerceOrder{

        $data["note"] = $order->getDetails();
        $data["shipping_country_code"] = Constant::codes[$order->getShippingCountry()];
        $data["shipping_name"] = $order->getShippingFirstName();
        $data["shipping_phone"] = $order->getPhone();
        $data["shipping_country"] = $order->getShippingCountry();
        $data["shipping_address"] = $order->getShippingAddressLine();
        $data["shipping_province_code"] = $order->getShippingCode();
        $data["shipping_city"] = $order->getShippingCity();
        $data["order_id"] = $order->getId();
        $data["order_number"] = $order->getNumber();

        foreach ($order->getCardProducts() as $cardProduct){
            $data["orderItemlist"][] = [
                "variantid" => $cardProduct->getProduct()->getVariantId(),
                "quantity" => $cardProduct->getQuantity()
            ];
        }

       $response = $this->sendData('add_order', 'POST', $data);
        if($response && $response['code'] == "0"){
            $order->setStatus(EcommerceOrder::CONFIRMED);
        }else{
            $order->setStatus(EcommerceOrder::CANCELED);
        }
        $this->entityManager->persist($order);

        return $order;
    }

    public function cancelOrder(EcommerceOrder $order, $details = null) : EcommerceOrder{

        $data['ids'] = $order->getId();
        $data['remarks'] = $details;
        $response = $this->sendData('cancel_orders', 'POST', $data);

        if($response && $response['code'] == 0){
            $order->setStatus(EcommerceOrder::CANCELED);
        }

        return $order;
    }


    public function sendData($path, $method, $data) {
        $timestamp = date_timestamp_get(new \DateTime());
        $md5 = md5($this::PUBLIC_KEY . $timestamp . $this::SECRET_KEY);

        try{
            $res = $this->client->request( $method, "https://openapi.eprolo.com/".$path.".html?sign=" . $md5 . "&timestamp=" . $timestamp, [
                RequestOptions::JSON => $data
            ]);
            return json_decode($res->getBody(), true);

        }catch (GuzzleException $exception){
            $this->logger->error($exception->getMessage());
        }

        return false;
    }


}