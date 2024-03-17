<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\Image;
use App\Entity\Nem;
use App\Form\ImageType;
use App\Repository\ImageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
    public function delete(EntityManagerInterface $manager  , Image $image): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        $cp = $image;
        $manager->remove($image);
        $manager->flush();
        if($cp->getArticle()){
            return $this->redirectToRoute('article_file',['id'=>$cp->getArticle()->getId()]);
        }
        elseif ($cp->getComment()){
            return $this->redirectToRoute('comment_image',['id'=>$cp->getComment()->getId()]);

        }
        else{
            return $this->redirectToRoute('app_article');
        }
    }
    #[Route('/image/article/{id}/new', name: 'add_image_article')]
    #[Route('/image/comment/{id}/new', name: 'add_image_comment')]
    public function create(ImageRepository $repository, Request $request, ImageRepository $imageRepository, $id, EntityManagerInterface $manager): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        $route = $request->attributes->get('_route');
        switch ($route) {
            case 'add_image_article' :
                $entity = Article::class;
                $setter = 'setArticle';
                $routeRedirect = 'article_file';
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
        return $this->redirectToRoute($routeRedirect,$param);

    }
}
