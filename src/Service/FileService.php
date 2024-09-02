<?php


namespace App\Service;


use App\Entity\CardProduct;
use App\Entity\Cart;
use App\Entity\EcommerceOrder;
use App\Repository\CartRepository;
use App\Repository\EcommerceOrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\VarDumper\VarDumper;

class FileService
{

    public function __construct(private readonly KernelInterface $kernel)
    {}

    public function uploadFile($file){
        $now = new \DateTime();
        $allowed = ['png', 'jpeg', 'jpg'];
        $dir = 'uploads/';
        $src = $this->kernel->getProjectDir() . '/public/' . $dir;

        if (!file_exists($src)) {
            mkdir($src, 0777, true);
        }

        $ogName = str_replace('.' . $file->getClientOriginalExtension(), '',  $file->getClientOriginalName());
        $name = $ogName . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $name = $file->getClientOriginalName();
        $search = [' ', '+', "(", ")", "'", '"', '/', '&', '<', '>', '€', '‘', '’', '“', '”', '–', '—', '¡', '¢', '£', '¤', '¥', '¦', '§', '¨', '©', 'ª', '«', '¬', '®', '¯', '°', '±', '²', '³', '´', 'µ', '¶', '·', '¸', '¹', 'º', '»', '¼', '½', '¾', '¿', 'À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', '×', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'Þ', 'ß', 'à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ð', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', '÷', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'þ', 'ÿ', 'Œ', 'œ', '‚', '„', '…', '™', '•', '˜'];
        $name = str_replace($search, '_', $name);
        $name = str_replace(" ", "_", $name);

        if (in_array(strtolower($file->getClientOriginalExtension()), $allowed)) {
            $name = $now->format('U') . '-' . $name;
            $file->move($src, $name);

            return ["status" => 200, 'path' => $dir.$name, 'message' => ''];
        }

        return ["status" => 201, 'path' => '', 'message' => "Format non valide. Les formats acceptés sont : png, jpeg, jpg"];

    }
}