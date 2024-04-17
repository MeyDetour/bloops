<?php

namespace App\Controller;

use App\Entity\Bloop;
use App\Entity\Comment;
use App\Entity\Image;
use App\Entity\Nem;
use App\Form\ImageType;
use App\Repository\ImageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ImageController extends AbstractController
{
    #[Route('/image', name: 'app_image')]
    public function index(): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        return $this->render('image/index.html.twig', [
            'controller_name' => 'ImageController',
        ]);
    }

    #[Route('/image/delete/{id}', name: 'delete_image')]
    public function delete(EntityManagerInterface $manager, Image $image): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        $cp = $image;
        $manager->remove($image);
        $manager->flush();
        if ($cp->getBloop()) {
            return $this->redirectToRoute('bloop_file', ['id' => $cp->getBloop()->getId()]);
        } elseif ($cp->getComment()) {
            return $this->redirectToRoute('comment_image', ['id' => $cp->getComment()->getId()]);

        } else {
            return $this->redirectToRoute('app_article');
        }
    }

    #[Route('/image/bloop/{id}/new', name: 'add_image_bloop')]
    #[Route('/image/comment/{id}/new', name: 'add_image_comment')]
    public function create(ImageRepository $repository, Request $request, ImageRepository $imageRepository, $id, EntityManagerInterface $manager): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        $route = $request->attributes->get('_route');
        switch ($route) {
            case 'add_image_bloop' :
                $entity = Bloop::class;
                $setter = 'setBloop';
                $routeRedirect = 'bloop_file';
                $param = ['id' => $id];
                break;


            case 'add_image_comment':
                $entity = Comment::class;
                $routeRedirect = 'comment_image';
                $setter = 'setComment';
                $param = ['id' => $id];
                break;
        }


        $image = new Image();
        $form = $this->createForm(ImageType::class, $image);

        $toAddedAnImage = $manager->getRepository($entity)->find($id);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $image->$setter($toAddedAnImage);
            $manager->persist($image);
            $manager->flush();

        };
        //  return $this->redirectToRoute($routeRedirectFormSubmitted, $param);
        return $this->redirectToRoute($routeRedirect, $param);

    }

    #[Route('/image/{id}', name: 'image_show')]
    public function showImage(Image $image)
    {
        $filePath = $this->getParameter('images_directory') . '/' . $image->getFileName();
        return new BinaryFileResponse($filePath);
    }
}
