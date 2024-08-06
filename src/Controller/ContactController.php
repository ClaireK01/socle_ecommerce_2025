<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/contact')]
class ContactController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em){
        $this->em = $em;
    }

    #[Route('/', name: 'app_contact')]
    public function index(Request $request, KernelInterface $kernel, EntityManagerInterface $em): Response
    {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);
        $siteKey = $_ENV['CAPTCHA_SITE_KEY'];
        $secretKey = $_ENV['CAPTCHA_SECRET_KEY'];

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $response = $request->request->get('h-captcha-response');
            if($response){
                $datas = ["secret" => $_ENV['CAPTCHA_SECRET_KEY'], "response" => $response];
                $res = json_decode($this->makeRequest($response, $datas, $secretKey));
                if($res->success){
                    $em->persist($contact);
                    $em->flush();
                    $this->addFlash('success', 'Votre message a bien été envoyer !');
                }else{
                    $this->addFlash('error', 'Captcha non valide');
                }

                return $this->redirectToRoute('app_contact');
            }
        }else{
            if($form->isSubmitted()){
                $this->addFlash('error', 'Oups! Il semble y avoir une erreur dans votre formulaire');
            }
        }

        return $this->render('app/contact.html.twig', [
            'form'=>$form->createView(),
            'siteKey' => $siteKey,
            'secretKey' => $secretKey,
        ]);
    }
    
    public function makeRequest($response, $datas, $secret)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $_ENV['VERIFY_URL'] . "?secret=${secret}&response=${response}");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array( 'Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_POSTFIELDS,$datas);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        //trouver comment ajouter verif ssl quand tmps
//                curl_setopt($ch, CURLOPT_SSLCERT, 'file://'. $kernel->getProjectDir(). '/config/cert/certificate.crt');
//                curl_setopt($ch, CURLOPT_SSLCERTPASSWD, "test");
        $server_output = curl_exec($ch);
        curl_close ($ch);

        return $server_output;
    }

}
