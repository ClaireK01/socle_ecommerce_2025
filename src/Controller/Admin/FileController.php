<?php

namespace App\Controller\Admin;

use App\Entity\Article;
use App\Entity\Picture;
use App\Form\ArticleType;
use App\Service\FileService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/manager/files')]
class FileController extends AbstractController
{
    #[Route('/', name: 'admin_file_index')]
    public function index(Request $request, EntityManagerInterface $entityManager, FileService $fileService, KernelInterface $kernel): Response
    {
        $upload = new UploadedFile($_FILES['img']['tmp_name'], $_FILES['pfp']['name'], $_FILES['pfp']['type']);

        $res = $fileService->uploadFile($upload);

        return new JsonResponse(['message' => $res['message'], 'path' => '../'.$res['path'] ], $res['status']);
    }

}
